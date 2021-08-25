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
	*	'validator' => ['column1' => [], 'column2' => [30]]
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

	private string $entity_property = '';

	/**
	* @param $model - This must be an object that extends model.
	*/
	public function __construct($model)
	{
		if (($model instanceof BaseModel) === false)
		{
			throw new InvalidArgumentException('Construct parameter model does not extend an instanceof model ' . var_export($model, true));
		}

		$this->model = $model;
	}

	/**
	* @param $validator - The validation function that will be run on the following columns
	* @param $rule_columns - The fields to search for from the model array
	* @param $params - Any additional parameters you may need for a specific column.
	* Ex ->addRule('validateAge', ['date_of_birth', 'registration_time'], ['date_of_birth' => 16])
	* Ex ->addRule('validateAge', ['date_of_birth', 'registration_time'], ['date_of_birth' => [16, 65]])
	* The 16 will be fed in as an additional parameter when the function validateAge is called on date_of_birth, but when validateAge is ran on registration_time
	* It uses the default parameter of the function.
	* @return void
	*/
	final public function addRule(string $validator, array $rule_columns, array $params = []) : void
	{
		foreach ($rule_columns as $column)
		{
			$this->rules[$validator][$column] = $params[$column] ?? [];
		}
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

		return $rules[$validator] ?? [];
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

			foreach ($rule_columns as $rule_column => $params)
			{
				/**
				 * This allows for developers to specify a parameter or parameters.
				 * @see addRule
				 * Ex ['date_of_birth' => 16]
				 * Ex ['date_of_birth' => [16, 65]]
				 */
				$shift_params = [$params];
				if (is_array($params))
				{
					$shift_params = $params;
				}
				array_unshift($shift_params, $fields[$rule_column]);

				$this->setCurrentEntityProperty($rule_column);

				$result = $result && call_user_func_array([$this, $validator], $shift_params);
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

	final protected function setCurrentEntityProperty(string $entity_property) : void
	{
		$this->entity_property = $entity_property;
	}

	final protected function getCurrentEntityProperty() : string
	{
		return $this->entity_property;
	}
}
