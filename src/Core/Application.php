<?php

use \Core\Router\RouterLib;
use \Core\Router\Router;
use \Core\Router\RouterURI;
use \Modules\Auth\Authentication;
use \Core\Response\ErrorResponse;
use \Core\Response\GenerateOutput;
use \Core\APIError;

class Application
{
	public static function run() : void
	{
		$collector = RouterLib::initRoutes();
		$uri = RouterLib::parseURI($collector);
		$router = new Router($uri, new Authentication());
		$router->routeAgent();
	}

	public static function exception_handler() : void
	{
		set_exception_handler(function ($exception)
		{
			$default_message = 'An error has occurred please try again later.';
			$error = new ErrorResponse(500, $default_message);
			if ($exception instanceof APIError)
			{
				if ($exception->getHttpCode() >= 500)
				{
					$error = new ErrorResponse($exception->getHttpCode(), $default_message);
				}
				else if ($exception->getHttpCode() < 500 && trim($exception->getMessage()) != '')
				{
					$error = new ErrorResponse($exception->getHttpCode(), $exception->getMessage());
				}
			}

			if ($error->getHttpCode() >= 500 && isDevEnv())
			{
				$error->setResponse([
					'message' => $exception->getMessage(),
					'file' => $exception->getFile(),
					'line' => $exception->getLine(),
					'trace' => $exception->getTrace()
				]);
			}

			/**
			* Clear all content so the JSON is not malformed.
			*/
			ob_clean();

			$generate = new GenerateOutput($error);
			exit($generate->output());
		});
	}

	/**
	* JSON Response
	* @see https://www.php.net/manual/en/function.error-get-last.php
	*/
	public static function shutdown_handler() : void
	{
		register_shutdown_function(function()
		{
			$error_last = error_get_last();
			if ($error_last === null)
			{
				return;
			}
			$error = new ErrorResponse(500, 'Unknown error has occurred');
			if (isDevEnv())
			{
				$error->setResponse($error_last);
			}

			/**
			* Clear all content so the JSON is not malformed.
			*/
			ob_clean();

			$generate = new GenerateOutput($error);
			exit($generate->output());
		});
	}

	public static function autoload() : void
	{
		spl_autoload_register(function ($class_name)
		{
			$filename = str_replace('\\', '/', $class_name) . '.php';
			$path = ROOT_PATH . '/src/' . $filename;

			if (file_exists($path))
			{
				require_once($path);
			}
		});

		/**
		* Composer Autoloading
		*/
		require_once(ROOT_PATH . '/vendor/autoload.php');
	}
}