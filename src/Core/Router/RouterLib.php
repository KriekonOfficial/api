<?php

namespace Core\Router;

use \GuzzleHttp\Psr7\ServerRequest;
use \FastRoute\Dispatcher;
use Core\Router\Exception\RouterException;
use Core\Router\Interfaces\RouteInterface;

class RouterLib
{
	public static function parseURI(Dispatcher $dispatcher) : RouterURI
	{
		$server = Router::getRequest()::getServer();

		$routeInfo = $dispatcher->dispatch($server->getMethod(), strtolower(rtrim($server->getUri()->getPath(), '/')));

		$uri = new RouterURI();
		switch ($routeInfo[0])
		{
			case Dispatcher::NOT_FOUND:
			throw new RouterException('Endpoint does not exist');

			case Dispatcher::METHOD_NOT_ALLOWED:
			throw new RouterException('Invalid method, please use the appropriate method for the request.', 405);

			case Dispatcher::FOUND:
				/**
				* Check to see if Route has a specific function/method we wanna use.
				* If none specified will default to "index"
				*/
				$route = explode('::', $routeInfo[1]);

				$uri->setControllerPath($route[0]);

				if (isset($route[1]))
				{
					$uri->setMethod($route[1]);
				}

				$arguments = [];
				foreach ($routeInfo[2] as $argument)
				{
					$arguments[] = $argument;
				}

				$uri->setParams($arguments);
				if ($server->getUri()->getQuery() != '')
				{
					$uri->setAdditionalParams($server->getQueryParams());
				}
			break;
		}

		return $uri;
	}

	public static function initRoutes(RouteInterface $routes) : Dispatcher
	{
		return \FastRoute\simpleDispatcher($routes);
	}
}