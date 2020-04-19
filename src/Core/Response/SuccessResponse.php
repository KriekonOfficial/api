<?php

namespace Core\Response;

use Core\Response\Interfaces\ResponseInterface;

class SuccessResponse implements ResponseInterface
{
	use Response;

	public function __construct(int $http_code, array $payload, string $message = '')
	{
		$this->setHttpCode($http_code);
		$this->setStatus('OK');
		$this->setResponse($payload);
		$this->setMessage($message);
	}
}