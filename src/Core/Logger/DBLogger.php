<?php

namespace Core\Logger;

class DBLogger implements Interfaces\LoggerInterface
{
	private const SUPPORTED_TABLES = ['log', 'log_ACCTID'];

	private $model;
	private $db_table = 'log';
	public function __construct(Model\LogModel $model)
	{
		$this->model = $model;

		$association = $model->getAssociation();
		if (!empty($association))
		{
			$table = 'log_' . strtoupper($association['type']);

			if (!in_array($table, self::SUPPORTED_TABLES))
			{
				throw new Exception\LoggerException('Log attempt against a unsupported table: ' . $table);
			}
			$this->db_table = $table;
		}
	}

    public function emergency() : void
    {
    	$this->_log();
    }

    public function alert() : void
    {
    	$this->_log();
    }

    public function critical() : void
    {
    	$this->_log();
    }

    public function error() : void
    {
    	$this->_log();
    }

    public function warning() : void
    {
    	$this->_log();
    }

    public function notice() : void
    {
    	$this->_log();
    }

    public function info() : void
    {
    	$this->_log();
    }

    public function debug() : void
    {
    	$this->_log();
    }

    public function log() : void
    {
    	$this->_log();
    }

    private function _log() : void
    {
    	$model = $this->model;

    	$LOGTYPEID = LogLevel::getLogType(LogLevel::getLogLevelDescription($model->getLogLevel()));
    	if ($model->getLogType() != '')
    	{
    		$LOGTYPEID = LogLevel::getLogType($model->getLogType());
    	}

    	$params = [
    		'LOGTYPEID' => $LOGTYPEID,
    		'level' => $model->getLogLevel(),
    		'date' => $model->getDate(),
    		'details' => $model->getMessage()
    	];

    	$association = $model->getAssociation();
    	if (!empty($association))
    	{
    		$params[$association['type']] = $association['type_value'];
    	}
    	\Core\Store\Database\Util\DBWrapper::insert($this->db_table, $params, $last_id, DEFAULT_LOG_DB);
    }
}