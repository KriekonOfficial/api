<?php

namespace Core\Request\Exception

class RequestException extends \Core\APIError
{
	public function __construct(string $message = '', int $http_code = 500)
	{
		parent::contruct($message, $http_code, \Core\ExceptionCode::REQUEST);
	}
}