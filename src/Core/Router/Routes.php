<?php

namespace Core\Router;

use \FastRoute\RouteCollector as Collector;
class Routes
{
	public function __invoke(Collector $route)
	{
		$this->version1($route);
	}

	private function version1(Collector $route)
	{
		$route->addGroup('/v1', function(Collector $route)
		{
			$this->userAccount($route);
		});
	}

	private function userAccount(Collector $route)
	{
		$controller = \Controllers\V1\User\Account::class;

		$route->addRoute('GET', '/user/account', $controller . '::info');
		$route->addGroup('/user/account', function(Collector $route) use ($controller)
		{
			$route->addRoute('POST', '/register',  $controller . '::register');
			$route->addRoute('GET', '/verify/{code}', $controller . '::verify');
			$route->addRoute('POST', '/login', $controller . '::login');
		});
	}
}