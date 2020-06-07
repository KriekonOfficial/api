<?php

namespace Core\Router\Interfaces;
use \FastRoute\RouteCollector;

interface RouteInterface
{
	public function __invoke(RouteCollector $collector);
}