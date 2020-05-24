<?php

namespace Core;

class Controller
{
	protected const DEFAULT_METADATA = ['required_auth' => true];
	protected array $request_metadata = ['*' => self::DEFAULT_METADATA];

	protected function addRequestMetadata(string $method, bool $required_auth = true) : void
	{
		$this->request_metadata[$method] = ['required_auth' => $required_auth];
	}

	public function getRequestMetadata(string $method) : array
	{
		if (!isset($this->request_metadata[$method]))
		{
			return $this->request_metadata['*'] ?? self::DEFAULT_METADATA;
		}
		return $this->request_metadata[$method];
	}

	public function isAuthRequired(string $method) : bool
	{
		$data = $this->getRequestMetadata($method);

		return $data['required_auth'] === true;
	}
}