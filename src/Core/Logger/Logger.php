<?php
namespace Core\Logger;

use Core\Logger\Exception\LoggerExeception;
use Core\Logger\Model\LogModel;

class Logger
{
	public static function log(LogModel $model, string $type = 'database') : void
	{
		switch ($type)
		{
			case 'database':
				$logger = new DBLogger($model);
			break;

			default:
				throw new LoggerExeception('Invalid Logger Type');
			break;
		}

		$level = strtolower(LogLevel::getLogLevelDescription($model->getLogLevel()));
		$logger->$level();
	}
}
