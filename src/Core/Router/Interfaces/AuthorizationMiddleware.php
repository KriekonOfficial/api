<?php

namespace Core\Router\Interfaces;

interface AuthorizationMiddleware
{
	/**
	* Child object of Model
	*/
	public function __construct($model);
}