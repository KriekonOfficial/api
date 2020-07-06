<?php
namespace Core;

use \Exception;

class ErrorBase
{
	/**
	* Stores User Viewable Errors.
	*/
	private array $error = [];

	/**
	* Stores Dashboard Viewable Errors.
	*/
	private array $error_administer = [];

	/**
	* Stores Dev Viewable Erros.
	*/
	private array $error_internal = [];

	private int $http_code = 400;

	////
	// Public routines
	////

	final public function setHttpCode(int $code) : void
	{
		$this->http_code = $code;
	}

	final public function getHttpCode() : int
	{
		return $this->http_code;
	}

	/**
	* Get the error array
	* @return array
	*/
	final public function getError() : array
	{
		return $this->error;
	}

	/**
	* Get the error_internal array
	* @return array
	*/
	final public function getInternalError() : array
	{
		return $this->error_internal;
	}

	/**
	* Get the error_administer array
	* @return array
	*/
	final public function getAdministerError() : array
	{
		return $this->error_administer;
	}

	/**
	* Get all user facing errors.
	* @param $type - What format should I output?
	* @return string
	*/
	final public function getErrors($type = 'text') : string
	{
		return $this->formatError($this->error, $type);
	}

	/**
	* Get all development facing errors.
	* @param $type - What format should I output?
	* @return string
	*/
	final public function getInternalErrors($type = 'text') : string
	{
		return $this->formatError($this->error_internal, $type);
	}

	/**
	* Get all administer facing errors.
	* @param $type - What format should I output?
	* @return string
	*/
	final public function getAdministerErrors($type = 'text') : string
	{
		return $this->formatError($this->error_administer, $type);
	}

	/**
	* Returns the last element in the error array.
	* @return string
	*/
	final public function getLastError() : string
	{
		$error = end($this->error);
		return $error !== false ? $error : '';
	}

	/**
	* Returns the last element in the error_interal array.
	* @return string
	*/
	final public function getLastInternalError() : string
	{
		$error = end($this->error_internal);
		return $error !== false ? $error : '';
	}

	/**
	* Returns the last element in the error_administer array.
	* @return sting
	*/
	final public function getLastAdministerError() : string
	{
		$error = end($this->error_administer);
		return $error !== false ? $error : '';
	}

	/**
	* Has a public error.
	* @return bool
	*/
	final public function hasError() : bool
	{
		return count($this->getError()) >= 1;
	}

	final public function clearErrors() : void
	{
		$this->setError([]);
	}

	final public function clearErrorAdminister() : void
	{
		$this->setErrorAdminister([]);
	}

	final public function clearErrorInternal() : void
	{
		$this->setErrorInternal([]);
	}

	final public function clearAllErrors() : void
	{
		$this->clearErrors();
		$this->clearErrorAdminister();
		$this->clearErrorInternal();
	}

	////
	// Protected routines
	////

	/**
	* Messages to show the user.
	* @param $message
	* @return void
	*/
	final protected function addError(string $message) : void
	{
		$this->error[] = $message;
	}

	/**
	* Messages to show administrators
	* @param $message
	* @return void
	*/
	final protected function addErrorAdminister(string $message) : void
	{
		$this->error_administer[] = $message;
	}

	/**
	* Messages to show internal developers.
	* @param $message
	* @return void
	*/
	final protected function addErrorInternal(string $message) : void
	{
		$this->error_internal[] = $message;
	}

	/**
	* @return void
	*/
	final protected function setError(array $error) : void
	{
		$this->error = $error;
	}

	/**
	* Set Administer error array
	* @return void
	*/
	final protected function setErrorAdminister(array $error) : void
	{
		$this->error_administer = $error;
	}

	/**
	* Set Internal developers array
	* @return void
	*/
	final protected function setErrorInternal(array $error) : void
	{
		$this->error_internal = $error;
	}

	////
	// Private routines
	////

	/**
	* @param $error_array - What error array are we formatting?
	* @param $type - How should we format the error?
	* @return string
	*/
	private function formatError($error_array, $type) : string
	{
		$message = '';
		switch ($type)
		{
			case 'text':
				$message = implode(', ', $error_array);
			break;

			default:
			throw new Exception('Format Error invalid type.');
		}
		return $message;
	}
}