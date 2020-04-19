<?php

namespace Core\Router\Interfaces;

interface AuthInterface
{
	public function checkAuth(\Core\Router\Route $route) : bool;

	public function getLastError() : string;
}