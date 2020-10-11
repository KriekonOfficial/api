<?php

namespace Modules\Auth;

use Core\ErrorBase;
use Core\Router\Interfaces\AuthInterface;
use Core\Router\CurrentRoute;

class ScriptAuth extends ErrorBase implements AuthInterface
{
	public function checkAuth(?CurrentRoute $route) : bool
	{
		return true;
	}

	public function getUser()
	{
		return null;
	}

	public function isAuthorized() : bool
	{
		return true;
	}
}
