<?php
namespace Core\Logger;

use Core\Logger\Exception\LoggerExeception;

class Logger
{
	public static function log(Model\LogModel $model, string $type = 'database') : void
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
