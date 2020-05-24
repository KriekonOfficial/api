<?php

namespace Core\Router;

class RouterURI
{
	private string $method = 'index';
	private array $params = [];
	private array $additional_params = [];

	private string $controller_path = '';

	public function setControllerPath(string $controller_path) : void
	{
		$this->controller_path = $controller_path;
	}

	////
	// Setters and Getters
	////

	public function getControllerPath() : string
	{
		return $this->controller_path;
	}

	public function setMethod(string $method) : void
	{
		$this->method = $method;
	}

	public function getMethod() : string
	{
		return $this->method;
	}

	public function setParams(array $params) : void
	{
		$this->params = $params;
	}

	public function getParams() : array
	{
		return $this->params;
	}

	public function setAdditionalParams(array $get) : void
	{
		$this->additional_params = $get;
	}

	public function getAdditionalParams() : array
	{
		return $this->additional_params;
	}
}