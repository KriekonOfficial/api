<?php

namespace Core\Logger;

use Core\Store\Database\Util\DBWrapper;
use Core\Logger\Interfaces\LoggerInterface;
use Core\Logger\Exception\LoggerException;
use Core\Logger\Model\LogModel;

class DBLogger implements LoggerInterface
{
	private const SUPPORTED_TABLES = ['log', 'log_USERID'];

	private $model;
	private $db_table = 'log';
	public function __construct(LogModel $model)
	{
		$this->model = $model;

		$association = $model->getAssociation();
		if (!empty($association))
		{
			$table = 'log_' . strtoupper($association['type']);

			if (!in_array($table, self::SUPPORTED_TABLES))
			{
				throw new LoggerException('Log attempt against a unsupported table: ' . $table);
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
		DBWrapper::insert($this->db_table, $params, $last_id, DEFAULT_LOG_DB);
	}
}
