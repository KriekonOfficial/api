<?php

namespace Core\Logger\Model;

use Core\Util\TimeUtils;

class LogModel
{
	protected string $message;

	protected int $level;

	protected string $log_type = '';

	protected array $association = [];
	protected string $date = '';

	public function __construct(string $message, int $level)
	{
		$this->setMessage($message);
		$this->setLogLevel($level);

		$this->setDate(date(TimeUtils::DATEFORMAT_STANDARD));
	}

	public function setMessage(string $message) : void
	{
		$this->message = $message;
	}

	public function getMessage() : string
	{
		return $this->message;
	}

	public function setLogLevel(int $level) : void
	{
		$this->level = $level;
	}

	public function getLogLevel() : int
	{
		return $this->level;
	}

	public function setLogType(string $log_type) : void
	{
		$this->log_type = $log_type;
	}

	public function getLogType() : string
	{
		return $this->log_type;
	}

	public function setAssociation(string $type, $type_value) : void
	{
		$this->association = ['type' => $type, 'type_value' => $type_value];
	}

	public function getAssociation() : array
	{
		return $this->association;
	}

	public function setDate(string $date) : void
	{
		$this->date = $date;
	}

	public function getDate() : string
	{
		return $this->date;
	}
}
