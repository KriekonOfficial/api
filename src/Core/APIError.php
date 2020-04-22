<?php

namespace Core;

use \Exception;

class APIError extends Exception
{
	protected $http_code = 500;

	public function __construct(string $message, int $http_code = 500, int $code = ExceptionCode::APIError)
	{
		$this->setHttpCode($http_code);
		parent::__construct($message, $code);
	}

	protected function setHttpCode(int $http_code)
	{
		$this->http_code = $http_code;
	}

	public function getHttpCode() : int
	{
		return $this->http_code;
	}
}