<?php

namespace Core\Router\Exception;

class DispatcherException extends \Core\APIError
{
	public function __construct(string $message = '', int $http_code = 500)
	{
		parent::__construct($message, $http_code, \Core\ExceptionCode::DISPATCHER);
	}
}