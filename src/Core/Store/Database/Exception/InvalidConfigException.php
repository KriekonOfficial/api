<?php

namespace Core\Store\Database\Exception;

use Core\APIError;
use Core\ExceptionCode;

class InvalidConfigException extends APIError
{
	public function __construct(string $message = '')
	{
		parent::__construct($message, 500, ExceptionCode::INVALID_CONFIG_DB);
	}
}