<?php

namespace Modules\Auth;

class Authentication extends \Core\ErrorBase implements \Core\Router\Interfaces\AuthInterface
{
	public function checkAuth(\Core\Router\Route $route) : bool
	{
		return true;
	}
}