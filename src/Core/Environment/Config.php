<?php

namespace Core\Environment;

use Core\Util\JSONWrapper;

class Config
{
	private static $_instance = null;

	private array $_config;
	private string $_file_path;

	private function __construct(string $file_path)
	{
		$json = @file_get_contents($file_path);

		if ($json === false)
		{
			throw new EnvironmentException('Unable to find config at path: ' . $file_path);
		}

		$decode = JSONWrapper::decode($json);

		if (json_last_error() !== JSON_ERROR_NONE || $decode === null)
		{
			throw new EnvironmentException('There has been an error parsing the input. JSON Error: ' . json_last_error_msg());
		}
		$this->_config = $decode;
		$this->_file_path = $file_path;
	}

	public function get(string $config_option)
	{
		if (!isset($this->_config[$config_option]))
		{
			throw new EnvironmentException('Config Option: ' . $config_option . ' does not exist inside the config! See file_path: ' . $this->_file_path);
		}
		return $this->_config[$config_option];
	}

	public static function getConfig() : self
	{
		return self::$_instance;
	}

	public static function setConfig(string $file_path) : void
	{
		if (self::$_instance !== null)
		{
			throw new EnvironmentException('Config Instance already exists! Only one instance of a config can exist.');
		}
		self::$_instance = new Config($file_path);
	}
}
