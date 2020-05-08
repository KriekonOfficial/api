<?php

namespace Modules\Auth;

use Core\ErrorBase;
use Core\Router\Interfaces\AuthInterface;
use Core\Router\Route;

class Authentication extends ErrorBase implements AuthInterface
{
	public function checkAuth(Route $route) : bool
	{
		return true;
	}
}