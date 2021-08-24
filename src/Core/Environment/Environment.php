<?php

namespace Core\Environment;

class Environment
{
	public const LOCAL = 'local';
	public const DEV = 'development';
	public const PROD = 'production';

	public static function isDevEnv() : bool
	{
		if (!Config::isInitialized())
		{
			throw new EnvironmentException('Environment is not initialized!');
		}
		return Config::getConfig()->get('environment') === self::DEV;
	}

	public static function isProdEnv() : bool
	{
		if (!Config::isInitialized())
		{
			throw new EnvironmentException('Environment is not initialized!');
		}
		return Config::getConfig()->get('environment') === self::PROD;
	}

	public static function isLocalEnv() : bool
	{
		if (!Config::isInitialized())
		{
			throw new EnvironmentException('Environment is not initialized!');
		}
		return Config::getConfig()->get('environment') === self::LOCAL;
	}
}
