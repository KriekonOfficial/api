<?php

namespace Classes;

use Core\Router\Interfaces\RouteInterface;
use \FastRoute\RouteCollector as Collector;

class Routes implements RouteInterface
{
	public function __invoke(Collector $route)
	{
		$route->addRoute('POST', '/mail/send', \Controllers\MailController::class . '::send');
		$this->version1($route);
	}

	private function version1(Collector $route)
	{
		$route->addGroup('/v1', function (Collector $route)
		{
			$this->user($route);
		});
	}

	private function user(Collector $route)
	{
		$route->addGroup('/user', function (Collector $route)
		{
			$this->userRoot($route);
			$this->userStatus($route);
		});
	}

	private function userRoot(Collector $route)
	{
		$controller = \Controllers\V1\User\User::class;

		$route->addRoute('GET', '', $controller . '::info');
		$route->addRoute('POST', '', $controller . '::register');
		$route->addRoute('GET', '/verify/{code}', $controller . '::verify');
		$route->addRoute('POST', '/login', $controller . '::login');
	}

	private function userStatus(Collector $route)
	{
		$controller = \Controllers\V1\User\Status::class;

		$route->addGroup('/status', function (Collector $route) use ($controller)
		{
			$route->addRoute('GET', '', $controller . '::listStatus');
			$route->addRoute('GET', '/{id}', $controller . '::getStatus');
			$route->addRoute('POST', '', $controller . '::createStatus');
			$route->addRoute('PUT', '/{id}', $controller . '::updateStatus');
			$route->addRoute('DELETE', '/{id}', $controller . '::deleteStatus');
		});
	}
}
