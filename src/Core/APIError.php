<?php

namespace Core;

use \Exception;

class APIError extends Exception
{
	protected $http_code = 500;

	/**
	* Anything greater than or equal to http code 500 will be masked behind a hidden message to the user.
	*/
	public function __construct(string $message, int $http_code = 500, int $code = ExceptionCode::APIError)
	{
		$this->setHttpCode($http_code);
		parent::__construct($message, $code);
	}

	final public function setHttpCode(int $http_code)
	{
		$this->http_code = $http_code;
	}

	final public function getHttpCode() : int
	{
		return $this->http_code;
	}
}