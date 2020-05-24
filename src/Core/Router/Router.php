<?php

namespace Core\Router;

use Core\Request\Request;
use Core\Router\Exception\RouterException;
use Core\Router\Interfaces\AuthInterface;
use Core\Response\GenerateOutput;
use \ReflectionClass;

class Router
{
	private static $uri = null;
	private static $request = null;

	private $route;

	public function __construct(RouterURI $uri, AuthInterface $auth)
	{
		self::$uri = $uri;
		$this->setRoute($this->checkRoute($uri, $auth));
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

	public function routeAgent() : void
	{
		$route = $this->getRoute();

		$dispatch = new Dispatcher();
		$response = $dispatch->dispatch($route);

		$generate = new GenerateOutput($response);
		echo $generate->output();
	}

	public function getRoute() : CurrentRoute
	{
		return $this->route;
	}

	public function setRoute(CurrentRoute $route) : void
	{
		$this->route = $route;
	}

	private function checkRoute(RouterURI $uri, AuthInterface $auth) : CurrentRoute
	{
		if (!class_exists($uri->getControllerPath()))
		{
			throw new RouterException('Endpoint does not exist');
		}

		$reflection_class = new ReflectionClass($uri->getControllerPath());

		if (!$reflection_class->isSubClassOf('\\Core\\Controller'))
		{
			throw new RouterException($uri->getControllerPath() . ' does not extend Controller', 501);
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

		$controller = $reflection_class->newInstance();

		$route = new CurrentRoute($uri, self::getRequest(), $reflection_class, $reflection_method);

		if ($controller->isAuthRequired($uri->getMethod()) && !$auth->checkAuth($route))
		{
			throw new RouterException($auth->getLastError(), 401);
		}
		return $route;
	}
}