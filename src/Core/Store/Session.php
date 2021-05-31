<?php

namespace Core\Store;

use Core\Environment\Environment;
use Core\Util\TimeUtils;

class Session
{
	public static function configure() : void
	{
		$sessioninfo = [
			'lifetime' => TimeUtils::ONE_HOUR,
			'path'     => '/',
			'domain'   => '.kriekon.com',
			'secure'   => Environment::isDevEnv() ? false : true,
			'httponly' => true,
			'samesite' => 'LAX'
		];

		session_name(Environment::isDevEnv() ? 'DEV_K_SID' : 'K_SID');
		session_set_cookie_params($sessioninfo);
	}

	/**
	* @param string $key
	* @param $default - If the session variable is not set, fall back to a default.
	* @return mixed
	*/
	public static function get(string $key, $default = null)
	{
		return $_SESSION[$key] ?? $default;
	}

	/**
	* @param string $key
	* @param mixed $value
	*/
	public static function set(string $key, $value) : void
	{
		$_SESSION[$key] = $value;
	}

	public static function delete(string $key) : void
	{
		unset($_SESSION[$key]);
	}

	public static function has(string $key) : bool
	{
		return array_key_exists($key, $_SESSION);
	}

	/**
	 * If a session exists destroy the session and regen the token.
	 */
	public static function start() : void
	{
		$regen_token = false;
		if (session_status() === PHP_SESSION_ACTIVE)
		{
			$regen_token = true;
			self::destroy();
		}

		session_start();

		if ($regen_token) session_regenerate_id(true);
	}

	public static function destroy() : void
	{
		if (session_status() === PHP_SESSION_ACTIVE)
		{
			$_SESSION = [];
		}

		@session_destroy();
	}
}
