<?php

namespace Core\Request;

use Core\Router\Router;
use Core\Request\Exception\RequestException;
use \GuzzleHttp\Psr7\ServerRequest;
use Core\Util\JSONWrapper;
use Core\Router\Interfaces\AuthInterface;

class Request
{
	private static $server = null;

	private $auth = null;

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

	public static function getRequestIP() : string
	{
		return self::getServer()->getServerParams()['REMOTE_ADDR'];
	}

	public function getAuth() : ?AuthInterface
	{
		return $this->auth;
	}

	public function setAuth(AuthInterface $auth) : void
	{
		$this->auth = $auth;
	}

	public function getRequestInput() : RequestInput
	{
		$input = (string)self::getServer()->getBody();

		if (empty($input))
		{
			throw new RequestException('Request must be given a JSON. Please refer to documentation.', 400);
		}

		$decoded_json = JSONWrapper::decode($input);

		if (json_last_error() !== JSON_ERROR_NONE || $decoded_json === null)
		{
			throw new RequestException('There has been an error parsing the input. JSON Error: ' . json_last_error_msg(), 400);
		}

		return new RequestInput($decoded_json);
	}
}
