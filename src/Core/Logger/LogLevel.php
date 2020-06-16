<?php
namespace Core\Logger;

use Core\Logger\Exception\LoggerException;

class LogLevel
{
	public const EMERGENCY = 1;
	public const ALERT = 2;
	public const CRITICAL = 3;
	public const ERROR = 4;
	public const WARNING = 5;
	public const NOTICE = 6;
	public const INFO = 7;
	public const DEBUG = 8;
	public const LOG = 9;

	public const LOG_TYPES = [
		'emergency'      => 1,
		'alert'          => 2,
		'critical'       => 3,
		'error'          => 4,
		'warning'        => 5,
		'notice'         => 6,
		'info'           => 7,
		'debug'          => 8,
		'log'            => 9,
		'account_login'  => 10,
		'account_logout' => 11,
		'status_delete' => 12
	];

	public static function getLogLevelDescription(int $level) : string
	{
		$description = '';
		switch ($level)
		{
			case self::EMERGENCY:
				$description = 'Emergency';
			break;

			case self::ALERT:
				$description = 'Alert';
			break;

			case self::CRITICAL:
				$description = 'Critical';
			break;

			case self::ERROR:
				$description = 'Error';
			break;

			case self::WARNING:
				$description = 'Warning';
			break;

			case self::NOTICE:
				$description = 'Notice';
			break;

			case self::INFO:
				$description = 'Info';
			break;

			case self::DEBUG:
				$description = 'Debug';
			break;

			case self::LOG:
				$description = 'Log';
			break;

			default:
				throw new LoggerException('Invalid Log Level type selected: ' . $level);
			break;
		}
		return $description;
	}

	public static function getLogType(string $log_type) : int
	{
		$log_type = strtolower($log_type);
		if (!isset(self::LOG_TYPES[$log_type]))
		{
			throw new LoggerException('Invalid Log type selected: ' . $log_type);
		}
		return self::LOG_TYPES[$log_type];
	}
}
