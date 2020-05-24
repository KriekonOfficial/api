<?php

namespace Modules\Auth;

use Core\ErrorBase;
use Core\Router\Interfaces\AuthInterface;
use Core\Router\CurrentRoute;

class Authentication extends ErrorBase implements AuthInterface
{
	public function checkAuth(CurrentRoute $route) : bool
	{
		$this->addError('Authentication required to make this request.');
		return false;
	}
}