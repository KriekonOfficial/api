<?php

namespace Classes;

use Core\Router\Interfaces\RouteInterface;
use \FastRoute\RouteCollector as Collector;

class Routes implements RouteInterface
{
	public function __invoke(Collector $route)
	{
		$this->version1($route);
	}

	private function version1(Collector $route)
	{
		$route->addGroup('/v1', function(Collector $route)
		{
			$this->user($route);
		});
	}

	private function user(Collector $route)
	{
		$route->addGroup('/user', function(Collector $route)
		{
			$this->userAccount($route);
			$this->userStatus($route);
		});
	}

	private function userAccount(Collector $route)
	{
		$controller = \Controllers\V1\User\Account::class;

		$route->addRoute('GET', '/account', $controller . '::info');
		$route->addGroup('/account', function(Collector $route) use ($controller)
		{
			$route->addRoute('POST', '/register',  $controller . '::register');
			$route->addRoute('GET', '/verify/{code}', $controller . '::verify');
			$route->addRoute('POST', '/login', $controller . '::login');
		});
	}

	private function userStatus(Collector $route)
	{
		$controller = \Controllers\V1\User\Status::class;

		$route->addRoute('GET', '/status', $controller . '::listStatus');
		$route->addRoute('GET', '/status/{id}', $controller . '::getStatus');

		$route->addRoute('POST', '/status', $controller . '::createStatus');
	}
}