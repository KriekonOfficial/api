<?php

namespace Core\Logger\Exception;

use Core\APIError;
use Core\ExceptionCode;

class LoggerException extends APIError
{
	public function __construct(string $message = '')
	{
		parent::__construct($message, 500, ExceptionCode::LOGGER);
	}
}