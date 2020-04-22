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

	/**
	* Rules that the validators will run against the fields.
	* [
	*	'validator' => ['column1', 'column2']
	* ]
	*/
	private array $rules = [];

	/**
	* Will continue to validate the rest of the validator values if one fails
	* But will not validate any other validators if 1 fails.
	*/
	protected bool $stop_on_first_validator_fail = true;

	/**
	* If any rule in the validator fails, this will fail the whole function.
	* If this is set to true.
	*/
	protected bool $stop_on_first_rule_fail = false;

	/**
	* @param $model - This must be an object that extends model.
	*/
	public function __construct($model)
	{
		if (($model instanceof Model) === false)
		{
			throw new InvalidArgumentException('Construct parameter model does not extend an instanceof model ' . var_export($model, true));
		}

		$this->model = $model;
	}

	/**
	* @param $validator - The validation function that will be run on the following columns
	* @param $rule_columns - The fields to search for from the model array
	* @return void
	*/
	final public function addRule(string $validator, array $rule_columns) : void
	{
		$this->rules[$validator] = $rule_columns;
	}

	final public function getRules() : array
	{
		return $this->rules;
	}

	/**
	* Gets the rule columns that are assigned to the validation function.
	* @param $validator
	* @return array
	*/
	final public function getRuleColumns(string $validator) : array
	{
		$rules = $this->getRules();
		if (isset($rules[$validator]))
		{
			return $rules[$validator];
		}
		return [];
	}

	final public function getModel()
	{
		return $this->model;
	}

	/**
	* Validate the model values based on the validators and the rule column's specified
	* @return bool true on succes or false on failure
	*/
	public function validate() : bool
	{
		$failure = true;
		$fields = $this->getModel()->toArray();

		foreach ($this->getRules() as $validator => $rule_columns)
		{
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