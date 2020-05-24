<?php

namespace Core\Router;

use \GuzzleHttp\Psr7\ServerRequest;
use \FastRoute\RouteCollector;
use \FastRoute\Dispatcher;
use Core\Router\Exception\RouterException;

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

	public static function initRoutes() : Dispatcher
	{
		$dispatcher = \FastRoute\simpleDispatcher(function(RouteCollector $route)
		{
			$route->addGroup('/v1', function(RouteCollector $route)
			{
				$route->addRoute('GET', '/user/account', \Controllers\V1\User\Account::class . '::info');
				$route->addGroup('/user/account', function(RouteCollector $route)
				{
					$controller = \Controllers\V1\User\Account::class;
					$route->addRoute('GET', '/', $controller . '::info');
					$route->addRoute('POST', '/register/{name}',  $controller . '::register');
					$route->addRoute('GET', '/verify/{code}', $controller . '::verify');
					$route->addRoute('POST', '/login', $controller . '::login');
				});
			});
		});
		return $dispatcher;
	}
}