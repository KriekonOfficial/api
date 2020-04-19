<?php

namespace Core\Entity\Exception;

use Core\APIError;

class EntityException extends APIError
{
	public function __construct(string $message, int $http_code = 500)
	{
		parent::__construct($message, $http_code);
	}
}