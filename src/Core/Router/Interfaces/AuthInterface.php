<?php

namespace Core\Router\Interfaces;

use Core\Router\CurrentRoute;

interface AuthInterface
{
	public function checkAuth(CurrentRoute $route) : bool;

	public function getUser();

	public function isAuthorized() : bool;

	public function getLastError() : string;
}
