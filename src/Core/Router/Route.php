<?php

namespace Core\Router;

use Core\Request\Request;
use \ReflectionClass;
use \ReflectionMethod;

class Route
{
	private $reflection_class;
	private $reflection_method;
	private $uri;
	private $request;

	public function __construct(
		RouterURI $uri,
		Request $request,
		ReflectionClass $class,
		ReflectionMethod $method
	)
	{
		$this->setRouterURI($uri);
		$this->setRequest($request);
		$this->setReflectionClass($class);
		$this->setReflectionMethod($method);
	}

	public function setRouterURI(RouterURI $uri) : void
	{
		$this->uri = $uri;
	}

	public function getRouterURI() : RouterURI
	{
		return $this->uri;
	}

	public function setRequest(Request $request) : void
	{
		$this->request = $request;
	}

	public function getRequest() : Request
	{
		return $this->request;
	}

	public function setReflectionClass(ReflectionClass $class) : void
	{
		$this->reflection_class = $class;
	}

	public function getReflectionClass() : ReflectionClass
	{
		return $this->reflection_class;
	}

	public function setReflectionMethod(ReflectionMethod $method) : void
	{
		$this->reflection_method = $method;
	}

	public function getReflectionMethod() : ReflectionMethod
	{
		return $this->reflection_method;
	}
}