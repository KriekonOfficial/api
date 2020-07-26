<?php

use \Core\Response\ErrorResponse;
use \Core\Response\GenerateOutput;
use \Core\APIError;

class Application
{
	private static $autoloaded = false;

	public static function error_handler() : void
	{
		set_error_handler(function ($error_number, $error, $error_file, $error_line)
		{
			error_clear_last();
			var_dump($error_number);
			exit();
		});
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
		register_shutdown_function(function ()
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
		if (self::$autoloaded === true)
		{
			throw new Exception('Unable to autoload our application twice.');
		}

		define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

		require(ROOT_PATH . '/src/includes/config.php');
		require(ROOT_PATH . '/src/includes/global_constants.php');
		require(ROOT_PATH . '/src/includes/global_functions.php');

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

		self::$autoloaded = true;
	}
}
