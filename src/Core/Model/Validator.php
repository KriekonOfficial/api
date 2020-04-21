<?php

namespace Core\Model;

use \InvalidArgumentException;
use Core\ErrorBase;
use Core\Logger\Logger;
use Core\Logger\Model\LogModel;
use Core\Logger\LogLevel;

class Validator extends ErrorBase
{
	protected $model;

	private array $validators = [];

	private array $rules = [];

	protected bool $stop_on_first_validator_fail = true;

	protected bool $stop_on_first_rule_fail = false;

	public function __construct($model)
	{
		if (($model instanceof Model) === false)
		{
			throw new InvalidArgumentException('Construct parameter model does not extend an instanceof model ' . var_export($mode, true));
		}

		$this->model = $model;
	}

	final public function addRule(string $validator, array $rule_columns) : void
	{
		$this->rules[$validator] = $rule_columns;
	}

	final public function getRules() : array
	{
		return $this->rules;
	}

	final public function getRuleColumns(string $validator) : array
	{
		$rules = $this->getRules();
		if (isset($rules[$validator]))
		{
			return $rules[$validator];
		}
		return [];
	}

	final public function setValidators(array $validators) : void
	{
		$this->validators = $validators;
	}

	final public function addValidator(string $validator_function) : void
	{
		$this->validators[] = $validator_function;
	}

	final public function getValidators() : array
	{
		return $this->validators;
	}

	final public function getModel()
	{
		return $this->model;
	}

	public function validate() : bool
	{
		$failure = true;
		$fields = $this->getModel()->toArray();

		foreach ($this->getValidators() as $validator)
		{
			$rule_columns = $this->getRuleColumns($validator);

			$result = true;

			foreach ($rule_columns as $rule_column)
			{
				$result = $result && call_user_func([$this, $validator], $fields[$rule_column]);
				if ($result === false && $this->isStopOnRuleFail())
				{
					break 2;
				}
			}

			if ($result === false)
			{
				$failure = false;
			}

			if (!$result && $this->isStopOnValidatorFail())
			{
				break;
			}
		}

		$error_fail = count($this->getError()) === 0;
		if ($error_fail === true && $failure === false)
		{
			$this->addError('Unknown validation error has occurred.');
			$logmodel = new LogModel('Validation method returned false without giving an error. Class: ' . get_class($this), LogLevel::ERROR);
			Logger::log($logmodel);

			return false;
		}

		return $error_fail;
	}

	final protected function isStopOnValidatorFail() : bool
	{
		return $this->stop_on_first_validator_fail;
	}

	final protected function isStopOnRuleFail() : bool
	{
		return $this->stop_on_first_rule_fail;
	}
}