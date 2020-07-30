<?php
class StreamingProcess extends DBObject
{
	static $log_storage_path = '/home/sites/vultr.com/process_output/';

	var $db_tablename = 'streaming_process';
	var $db_key = array('PROCESSID');
	var $db_fields = array(
		'PROCESSID',
		'command',
		'date_started',
		'started_by',
		'pid',
		'object_type',
		'object_id',
		'progress',
		'host',			// what host was this process executed on?
	);
	var $ignore_flag = true;
	var $ignore_flag_update = false;

	public function __construct($params = null)
	{
		if (isset($params['PROCESSID']) && $params['PROCESSID'] === '')
		{
			return false;
		}
		parent::init($params);
	}

	/**
	 * @param string $command
	 * @param string $object_type
	 * @param string $object_id
	 * @param bool $append_PROCESSID - if true, append --PROCESSID=$PROCESSID to the command line
	 * @return bool
	 */
	public function execute($command, $object_type = '', $object_id = '', $append_PROCESSID = false)
	{
		if ($this->isInitialized())
		{
			$this->error[] = 'Process already running';
			return false;
		}

		if (!$this->add(array(
			'command'     => 'placeholder',
			'object_type' => $object_type,
			'object_id'   => $object_id,
		)))
		{
			return false;
		}

		$pid = 0;
		$ok = $this->executeOnly($command, $this['PROCESSID'], $append_PROCESSID, $pid);
		$error = $this->error;
		$error_internal = $this->error_internal;

		// Remove passwords from command line before we set it in the DB
		$clean_command = preg_replace('/--password\s*[^ ]+/i', '--password removed', $command);
		$this->set(array('pid'=>$pid, 'command' => $clean_command));
		if (!$ok)
		{
			// When executeOnly fails, always set pid and command in db, but return errors from executeOnly
			$this->error = $error;
			$this->error_internal = $error_internal;
		}

		return $ok;
	}

	/**
	 * Execute without database integration, this can be used when building APIs that launch StreamingProcess locally.
	 */
	public function executeOnly($command, $PROCESSID, $append_PROCESSID = false, &$out_pid = null)
	{
		$out_pid = 0;

		$logfile = $this->getLogFile($PROCESSID);
		$basedirectory = dirname($logfile);

		$screenrc = $basedirectory.'/'.$PROCESSID.'.rc';
		@mkdir($basedirectory);
		@chmod($basedirectory, 0777);
		@file_put_contents($screenrc, 'logfile '.$logfile);

		if ($append_PROCESSID)
		{
			$command .= ' --PROCESSID='.$PROCESSID;
		}

		$command = '/usr/bin/screen -c '.$screenrc.' -dmS '.$PROCESSID.' -L sh -c '.escapeshellarg('{ '.$command.' ; echo -e "\nEXIT CODE $?" ; }');
		exec($command, $output, $return);

		if ($return != 0)
		{
			$error = 'Screen terminated with error code '.$return.' '.implode("\n", $output);
			$this->error[] = $error;
			if (isset($GLOBALS['logger']))
			{
				$GLOBALS['logger']->log(array('type' => 'generic error', 'details' => $error, 'object_ref' => $this));
			}
			return false;
		}

		$pid = exec('/usr/bin/screen -ls | /bin/grep '.$PROCESSID.' | /bin/awk \'{print $1}\' | /bin/cut -d . -f 1');

		if (!file_exists($screenrc) && !file_exists($logfile) && (int)$pid == 0)
		{
			$error = 'Screen environment sanity check failed (pid '.$pid.')';
			$this->error[] = $error;
			if (isset($GLOBALS['logger']))
			{
				$GLOBALS['logger']->log(array('type' => 'generic error', 'details' => $error, 'object_ref' => $this));
			}
			return false;
		}

		$out_pid = $pid;
		return true;
	}

	public function add($params)
	{
		if (!isset($params['PROCESSID']))
		{
			$params['PROCESSID'] = self::newPROCESSID();
		}

		$params['date_started'] = 'NOW()';
		$params['started_by'] = $GLOBALS['auth']->get('SUID');
		if (!isset($params['host']))
		{
			$params['host'] = exec('/bin/hostname');
		}

		return parent::add($params);
	}

	public function getViewURL($PROCESSID = null)
	{
		if ($PROCESSID === null)
		{
			$PROCESSID = $this['PROCESSID'];
		}

		return '/process/stream_output.php?PROCESSID='.$PROCESSID;
	}

	public function getLogFile($PROCESSID = null)
	{
		if ($PROCESSID === null)
		{
			$PROCESSID = $this['PROCESSID'];
		}

		if ($this->isInitialized())
		{
			if ($this['date_started'] === 'NOW()')
			{
				$res = DBWrapper::PExecute('select date_started from streaming_process where PROCESSID=?', array($this['PROCESSID']));
				$date = $res->fields['date_started'];
			}
			else
			{
				$date = $this['date_started'];
			}
			return StreamingProcess::$log_storage_path.'/'.strftime('%F', strtotime($date)).'/'.$PROCESSID;
		}

		return StreamingProcess::$log_storage_path.'/'.$PROCESSID;
	}

	public function isRunningRemote()
	{
		if (!$this->isInitialized())
		{
			return false;
		}

		// If process is running on this server we can get information directly
		if ($this->get('host') == exec('/bin/hostname'))
		{
			return $this->isRunning();
		}

		// If process is running on a different server we need to request the process information via api call
		if (!isset(StreamingProcess::streamingProcessGetAPIMap()[$this->get('host')]))
		{
			return false;
		}
		$curl = curl_init_ssl('http' . (isDevEnv()?'':'s') . '://' . StreamingProcess::streamingProcessGetAPIMap()[$this->get('host')]['api'] . '/process/streaming_process_is_running.php?PROCESSID=' . $this->get('PROCESSID'));
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 10,
		));
		$result = normalizeJSONResult(curl_exec($curl));
		if (!$result['result'])
		{
			return false;
		}
		return $result['result_data'];
	}

	public function isRunning()
	{
		/**
		*	We use a PID of 1 to indicate that the process has stopped (permanently).  Not terribly intuitive,
		*	but there's no real way a random userspace process can ever end up with a PID of 1
		*/
		if ($this['pid'] == 1)
		{
			return false;
		}

		if ($this['pid'] == 0)
		{
			$pid = exec('/usr/bin/screen -ls | /bin/grep '.$this['PROCESSID'].' | /bin/awk \'{print $1}\' | /bin/cut -d . -f 1');
			$this->set(array('pid'=>$pid));
		}

		/**
		*	Didn't find the pid?  We must be stopped
		*/
		if ($this['pid'] == 0)
		{
			return false;
		}

		if (!file_exists('/proc/'.$this['pid']))
		{
			$this->set(array('pid' => 1));
			return false;
		}

		return true;
	}

	/**
	*	Return the exit code of the process.  Note that this will block until the process exits (if it's still running)
	*/
	public function getExitCode()
	{
		while ($this->isRunning())
		{
			sleep(1);
		}

		if (!file_exists($this->getLogFile()))
		{
			return false;
		}

		$line = exec('/usr/bin/tail -n 1 '.escapeshellarg($this->getLogFile()));
		if (preg_match('/XIT CODE ([0-9]+)/i', $line, $matches))
		{
			return $matches[1];
		}
		return false;
	}

	public function getLastLines($num = 1)
	{
		if (!file_exists($this->getLogFile()))
		{
			return array();
		}

		$output = '';
		exec('/usr/bin/tail -n '.intVal($num).' '.escapeshellarg($this->getLogFile()), $output);
		return $output;
	}

	/**
	 * @return float
	 */
	public function getProgress()
	{
		return (float)$this->get('progress');
	}

	/**
	 * @param float $progress
	 * @return bool
	 */
	public function setProgress($progress)
	{
		$progress = (float)$progress;

		if ($progress < 0.00)
		{
			$progress = 0.00;
		}
		if ($progress > 100.00)
		{
			$progress = 100.00;
		}

		$this->set(array('progress' => $progress));
	}

	/**
	 * @return string
	 */
	static public function newPROCESSID()
	{
		return piduniqid();
	}

	/**
	 * We can use this map to:
	 *     - Authorize api requests for stream process information
	 *     - Direct streaming process api calls to the correct api server based on the "streaming_process::host" field
	 * @return array
	 */
	static public function streamingProcessGetAPIMap()
	{
		if (isDevEnv())
		{
			return array(
			'local-vultr' => array('ip' => '172.16.0.2', 'api' => 'local.webapi.vultr.com'));
		}
		return array(
		'administer4.vultr.com'  => array('ip' => '108.61.150.28', 'api' => 'webapi4.vultr.com'),
		'administer5.vultr.com'  => array('ip' => '64.237.34.251', 'api' => 'webapi5.vultr.com'),
		'administer6.vultr.com'  => array('ip' => '45.32.4.155', 'api' => 'webapi6.vultr.com'));
	}

	/**
	 * IP based authorization check for an administer server to check a process running on a different administer server.
	 * @param string $ip
	 * @return boolean
	 */
	static public function streamingProcessAuthIP($ip)
	{
		foreach (StreamingProcess::streamingProcessGetAPIMap() as $k => $v)
		{
			if ($v['ip'] == $ip)
			{
				return true;
			}
		}
		return false;
	}
}
