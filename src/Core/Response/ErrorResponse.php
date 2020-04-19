<?php

namespace Core\Response;

use Core\Response\Interfaces\ResponseInterface;

class ErrorResponse implements ResponseInterface
{
	use Response;

	public function __construct(int $http_code, string $message, array $payload = [])
	{
		$this->setHttpCode($http_code);
		$this->setMessage($message);
		$this->setResponse($payload);
		$this->setStatus('ERROR');
	}
}