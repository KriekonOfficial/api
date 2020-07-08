<?php

namespace Core;

use Core\Router\Interfaces\AuthorizationMiddleware;

class Controller
{
	protected const DEFAULT_METADATA = ['required_auth' => true, 'auth_middleware' => ''];
	protected array $request_metadata = ['*' => self::DEFAULT_METADATA];

	protected function addRequestMetadata(string $method, bool $required_auth = true) : void
	{
		$this->addRequiredAuth($method, $required_auth);
	}

	protected function addRequiredAuth(string $method, bool $required_auth = true) : void
	{
		$this->request_metadata[$method]['required_auth'] = $required_auth;
	}

	protected function addAuthorizationMiddleware(string $method, AuthorizationMiddleware $auth_middleware)
	{
		$this->request_metadata[$method]['auth_middleware'] = $auth_middleware;
	}

	public function getRequestMetadata(string $method) : array
	{
		if (!isset($this->request_metadata[$method]))
		{
			return $this->request_metadata['*'] ?? self::DEFAULT_METADATA;
		}
		return $this->request_metadata[$method];
	}

	public function isAuthenticationRequired(string $method) : bool
	{
		$data = $this->getRequestMetadata($method);

		return ($data['required_auth'] ?? false) === true;
	}

	public function hasAuthorizationMiddleware(string $method) : bool
	{
		$data = $this->getRequestMetadata($method);

		return ($data['auth_middleware'] ?? false) instanceof AuthorizationMiddleware;
	}
}