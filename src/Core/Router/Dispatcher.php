<?php

namespace Core\Router;

use Core\Response\Interfaces\ResponseInterface;
use Core\Router\Exception\DispatcherException;
use Core\APIError;
use \ArgumentCountError;
use \TypeError;
use \Exception;
use Core\Response\ErrorResponse;

class Dispatcher
{
	/**
	* @return ResponseInterface
	*/
	public function dispatch(CurrentRoute $route) : ResponseInterface
	{
		$controller = $route->getReflectionClass()->newInstance();

		$number_of_params = $route->getReflectionMethod()->getNumberOfParameters();

		if ($number_of_params < 1)
		{
			$response = $route->getReflectionMethod()->invoke($controller);
			return $response;
		}

		$arguments = $this->parseArguments($route);
		try
		{
			$response = $route->getReflectionMethod()->invokeArgs($controller, $arguments);
		}
		catch (ArgumentCountError $e)
		{
			throw new DispatcherException('Invalid request, not enough parameters. Please look to see required parameters for this request', 400);
		}
		catch (TypeError $e)
		{
			$string = 'A parameter does not match the appropriate data type for this endpoint, please refer to documentation.';

			preg_match('/Argument\s([0-9])/', $e->getMessage(), $matches);
			if (isset($matches[1]))
			{
				$argument = (int)$matches[1] - 1;
				if ($argument < 0)
				{
					$argument = 0;
				}

				foreach ($route->getReflectionMethod()->getParameters() as $parameter)
				{
					$type = $parameter->getType();
					if ($parameter->getPosition() == $argument && $type !== null)
					{
						if ($type->getName() === 'Core\Request\Request' || $parameter->getName() === 'get_params')
						{
							throw new APIError($e->getMessage());
						}

						$string = 'Parameter ' . $parameter->getName() . ' must be of the type ' . $type->getName() . ', please refer to documentation.';
					}
				}
				throw new DispatcherException($string, 400);
			}

			throw new TypeError($e->getMessage().". Trace: ".$e->getTraceAsString());
		}

		return $response;
	}

	private function parseArguments(CurrentRoute $route) : array
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
