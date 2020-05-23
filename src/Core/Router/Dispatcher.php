<?php

namespace Core\Router;

use Core\Response\Interfaces\ResponseInterface;
use Core\Router\Exception\DispatcherException;
use \ArgumentCountError;
use Core\Response\ErrorResponse;

class Dispatcher
{
	/**
	* @return ResponseInterface
	*/
	public function dispatch(Route $route) : ResponseInterface
	{
		$controller = $route->getReflectionClass()->newInstance();

		$number_of_params = $route->getReflectionMethod()->getNumberOfParameters();

		if ($number_of_params < 1)
		{
			$response = $route->getReflectionMethod()->invoke($controller);
		}
		else
		{
			$arguments = $this->parseArguments($route);
			try
			{
				$response = $route->getReflectionMethod()->invokeArgs($controller, $arguments);
			}
			catch (ArgumentCountError $e)
			{
				throw new DispatcherException('Invalid request, not enough parameters. Please look to see required parameters for this request');
			}
		}

		return $response;
	}

	private function parseArguments(Route $route) : array
	{
		$request_index = null;
		$get_index = null;
		foreach ($route->getReflectionMethod()->getParameters() as $parameter)
		{
			$type = $parameter->getType();
			if ($type !== null && $type->getName() === 'Core\Request\Request')
			{
				$request_index = $parameter->getPosition();
			}
			else if ($parameter->getName() === 'get_params')
			{
				$get_index = $parameter->getPosition();
			}
		}

		$arguments = [];
		if ($request_index !== null)
		{
			$arguments[$request_index] = $route->getRequest();
		}

		if ($get_index !== null)
		{
			$arguments[$get_index] = $route->getRouterURI()->getAdditionalParams();
		}

		foreach ($route->getRouterURI()->getParams() as $param)
		{
			$arguments[] = $param;
		}

		ksort($arguments);

		return $arguments;
	}
}