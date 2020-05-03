<?php

namespace Core\Request\Exception;

use Core\APIError;
use Core\ExceptionCode;

class RequestException extends APIError
{
	public function __construct(string $message = '', int $http_code = 500)
	{
		parent::__construct($message, $http_code, ExceptionCode::REQUEST);
	}
}