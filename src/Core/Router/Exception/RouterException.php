<?php

namespace Core\Router\Exception;

use Core\APIError;
use Core\ExceptionCode;

class RouterException extends APIError
{
	public function __construct(string $message = '', int $http_code =  404)
	{
		parent::__construct($message, $http_code, ExceptionCode::ROUTER);
	}
}