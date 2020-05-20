<?php

namespace Core\Request;

class RequestInput
{
	private array $record;
	public function __construct(array $input)
	{
		$this->record = $input;
	}

	/**
	* @return mixed|null
	*/
	public function get(string $key)
	{
		return $this->record[$key] ?? null;
	}
}