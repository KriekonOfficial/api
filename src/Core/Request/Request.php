<?php

namespace Core\Request;

use Core\Router\Router;
use Core\Request\Exception\RequestException;
use \GuzzleHttp\Psr7\ServerRequest;

class Request
{
	private static $server = null;

	public function __construct()
	{
		self::setServer();
	}

	public static function getServer() : ServerRequest
	{
		self::setServer();
		return self::$server;
	}

	private static function setServer() : void
	{
		if (self::$server === null)
		{
			self::$server = ServerRequest::fromGlobals();
		}
	}

	public function getRequestInput() : array
	{
		$input = (string)self::getServer()->getBody();

		/*if (!empty($_POST))
		{
			return $_POST;
		}*/

		$decoded_json = json_decode($input, true);

		if (json_last_error() !== JSON_ERROR_NONE || $decoded_json === null)
		{
			throw new RequestException('There has been an error parsing the JSON. JSON Error: ' . json_last_error_msg(), 400);
		}

		return $decoded_json;
	}
}