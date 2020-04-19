<?php

namespace Core\Logger\Exception;

use Core\ExceptionCode;

class LoggerException extends \Core\APIError
{
	public function __construct(string $message = '')
	{
		parent::__construct($message, 500, ExceptionCode::LOGGER);
	}
}