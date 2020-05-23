<?php

namespace Core;

class Controller extends ErrorBase
{
	protected const DEFAULT_METADATA = ['http_method' => '*', 'required_auth' => true];
	protected array $request_metadata = ['*' => self::DEFAULT_METADATA];

	protected function addRequestMetadata(string $method, string $http_method = '*', bool $required_auth = true) : void
	{
		$this->request_metadata[$method] = ['http_method' => $http_method, 'required_auth' => $required_auth];
	}

	public function getRequestMetadata(string $method) : array
	{
		if (!isset($this->request_metadata[$method]))
		{
			return $this->request_metadata['*'] ?? self::DEFAULT_METADATA;
		}
		return $this->request_metadata[$method];
	}

	public function isHttpMethodAccepted(string $method, string $http_method) : bool
	{
		$data = $this->getRequestMetadata($method);

		if ($data['http_method'] === '*')
		{
			return true;
		}

		$methods = preg_split('/(\s*,*\s*)*,+(\s*,*\s*)*/', strtoupper($data['http_method']));

		return in_array($http_method, $methods);
	}

	public function isAuthRequired(string $method) : bool
	{
		$data = $this->getRequestMetadata($method);

		return $data['required_auth'] === true;
	}
}