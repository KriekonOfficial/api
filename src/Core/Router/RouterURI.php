<?php

namespace Core\Router;

class RouterURI
{
	private array $ALLOWED_VERSIONS = [];
	private string $version = '';
	private string $directory = '';
	private string $controller = 'Index';
	private string $method = 'index';
	private array $params = [];
	private array $additional_params = [];

	private string $controller_path = '';

	public function __construct(array $ALLOWED_VERSIONS = [], string $version = '', string $directory = '')
	{
		if (!empty($ALLOWED_VERSIONS))
		{
			$this->setAllowedVersions($ALLOWED_VERSIONS);
		}

		if ($version != '')
		{
			$this->setVersion($version);
		}

		if ($directory != '')
		{
			$this->setDirectory($directory);
		}
	}

	public function setPropsVersionURL(array $url) : void
	{
		if (isset($url[1]))
		{
			$this->setDirectory(camelCase($url[1]));
		}

		$this->setControllerMethodParams($url, 2, 3);
		$this->setControllerPath();
	}

	public function setPropsRootURL(array $url) : void
	{
		$this->setVersion('Root');

		if (isset($url[0]))
		{
			$this->setDirectory(camelCase($url[0]));
		}

		$this->setControllerMethodParams($url, 1, 2);
		$this->setControllerPath();
	}

	private function setControllerPath() : void
	{
		$controller = "\\Controllers\\";
		if ($this->getVersion() != '')
		{
			$controller .= $this->getVersion() . "\\";
		}

		if ($this->getDirectory() != '')
		{
			$controller .= $this->getDirectory() . "\\";
		}

		$controller .= $this->getController();

		$this->controller_path = $controller;
	}

	private function setControllerMethodParams(array $url, int $controller_pos, int $method_pos) : void
	{
		if (isset($url[$controller_pos]))
		{
			$this->setController(camelCase($url[$controller_pos]));
		}

		if (isset($url[$method_pos]))
		{
			$this->setMethod(strtolower($url[$method_pos]));
		}

		$params = [];
		for ($index = $method_pos + 1; $index <= array_key_last($url); $index++)
		{
			$params[] = $url[$index];
		}

		if (!empty($params))
		{
			$this->setParams($params);
		}

		if (isset($url['additional_params']))
		{
			$this->setAdditionalParams($url['additional_params']);
		}
	}

	////
	// Setters and Getters
	////

	public function getControllerPath() : string
	{
		return $this->controller_path;
	}

	public function getMethodPath() : string
	{
		return $this->method_path;
	}

	public function setVersion(string $version) : void
	{
		$this->version = $version;
	}

	public function getVersion() : string
	{
		return $this->version;
	}

	public function setDirectory(string $directory) : void
	{
		$this->directory = $directory;
	}

	public function getDirectory() : string
	{
		return $this->directory;
	}

	public function setController(string $controller) : void
	{
		$this->controller = $controller;
	}

	public function getController() : string
	{
		return $this->controller;
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

	public function getAllowedVersions() : array
	{
		return $this->ALLOWED_VERSIONS;
	}

	public function setAllowedVersions(array $versions) : void
	{
		$this->ALLOWED_VERSIONS = $versions;
	}
}