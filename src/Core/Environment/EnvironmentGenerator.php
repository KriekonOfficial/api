<?php

namespace Core\Environment;

use Core\Environment\Model\DatabaseModel;
use Core\Util\JSONWrapper;

class EnvironmentGenerator
{
	private array $_config = [];

	private string $file_path;
	public function __construct(string $environment, string $file_path)
	{
		if (!in_array($environment, array(Environment::LOCAL, Environment::DEV, Environment::LIVE)))
		{
			throw new Exception('Invalid environment');
		}
		$this->addConfigOption('environment', $environment);

		$this->file_path = $file_path;
	}

	public function addDatabase(DatabaseModel $model)
	{
		$this->_config['databases'][$model->getDBName()] = $model;
	}

	public function addEncryptionKey(string $name, string $encryption_key)
	{
		$this->_config['encryption_keys'][$name] = $encryption_key;
	}

	public function addModel(string $key, $model)
	{
		if (!($model instanceof EnvironemtModel))
		{
			throw new Exception('Model does not extend EnvironmentModel');
		}
		$this->_config[$key][] = $model;
	}

	/**
	* @param $value - mixed
	*/
	public function addConfigOption(string $option, $value)
	{
		$this->_config[$option] =  $value;
	}

	public function generate() : void
	{
		$json = array();
		foreach ($this->_config as $key => $attribute)
		{
			if (!is_array($attribute))
			{
				$json[$key] = $attribute;
				continue;
			}

			$json[$key] = [];
			foreach ($attribute as $key2 => $value)
			{
				if (is_object($value))
				{
					$json[$key][$key2] = $value->toArray();
					continue;
				}

				$json[$key][$key2] = $value;
			}
		}
		var_dump($json);
		file_put_contents($this->file_path, JSONWrapper::json($json, JSON_PRETTY_PRINT));
	}
}
