<?php

namespace Core\Router\Exception;

use Core\ExceptionCode;

class RouterException extends \Core\APIError
{
	public function __construct(string $message = '', int $http_code =  404)
	{
		parent::__construct($message, $http_code, ExceptionCode::ROUTER);
	}
}