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

		if ($this->getHttpCode() >= 500)
		{
			/**
			 * TODO, we should probably move this to the exception handler. So we don't log errors that have been caught.
			 * @see https://www.php.net/manual/en/function.error-log
			 */
			if (php_sapi_name() == 'cli')
			{
				/**
				 * 3 message is appended to the file destination. A newline is not automatically added to the end of the message string.
				 */
				error_log($this->getMessage()."\nTrace:".$this->getTraceAsString().PHP_EOL, 3, '/var/log/kriekon/kriekon_api.log');
			}
			else
			{
				error_log($this->getMessage()."\nTrace:".$this->getTraceAsString());
			}
		}
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
