<?php

namespace Core\Router;

use Core\Request\Request;
use Core\Router\Exception\RouterException;

class Router
{
	private static $uri = null;
	private static $request = null;

	private $route;

	public function __construct(RouterURI $uri)
	{
		self::$uri = $uri;
		$this->setRoute($this->checkRoute($uri));
	}

	public static function getRouterURI() : RouterURI
	{
		if (self::$uri === null)
		{
			throw new RouterException('URI does not exist', 501);
		}

		return self::$uri;
	}

	public static function getRequest() : Request
	{
		if (self::$request === null)
		{
			self::$request = new Request();
		}
		return self::$request;
	}

	public function routeAgent(\Core\Router\Interfaces\AuthInterface $auth) : void
	{
		$route = $this->getRoute();
		if (!$auth->checkAuth($route))
		{
			throw new RouterException($auth->getLastError());
		}

		$dispatch = new Dispatcher();
		$response = $dispatch->dispatch($route);

		$generate = new \Core\Response\GenerateOutput($response);
		echo $generate->output();
	}

	public function getRoute() : Route
	{
		return $this->route;
	}

	public function setRoute(Route $route) : void
	{
		$this->route = $route;
	}

	private function checkRoute(RouterURI $uri) : Route
	{
		if (!class_exists($uri->getControllerPath()))
		{
			throw new RouterException('Endpoint does not exist');
		}

		$reflection_class = new \ReflectionClass($uri->getControllerPath());

		if (!$reflection_class->isSubClassOf('\\Core\\Controller'))
		{
			throw new RouterException('Controller does not extend Controller', 501);
		}

		if (!$reflection_class->hasMethod($uri->getMethod()))
		{
			throw new RouterException('Endpoint does not exist');
		}

		$reflection_method = $reflection_class->getMethod($uri->getMethod());

		if (!$reflection_method->isPublic())
		{
			throw new RouterException('Endpoint does not exist');
		}

		$route = new Route($uri, self::getRequest(), $reflection_class, $reflection_method);
		return $route;
	}
}