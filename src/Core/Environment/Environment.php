<?php

namespace Core\Environment;

class Environment
{
	public const LOCAL = 'local';
	public const DEV = 'development';
	public const LIVE = 'live';

	public static function isDevEnv() : bool
	{
		return Config::getConfig()->get('environment') === self::DEV;
	}

	public static function isLiveEnv() : bool
	{
		return Config::getConfig()->get('environment') === self::LIVE;
	}

	public static function isLocalEnv() : bool
	{
		return Config::getConfig()->get('environment') === self::LOCAL;
	}
}
