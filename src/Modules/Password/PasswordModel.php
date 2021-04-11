<?php

namespace Modules\Password;

use Core\Model\BaseModel;
use \BadMethodCallException;

class PasswordModel extends BaseModel
{
	protected string $password;

	public function __construct(string $password)
	{
		$this->password = $password;
	}

	public function getPassword() : string
	{
		return $this->password;
	}

	public function generatePasswordHash(int $cost = 12) : string
	{
		return password_hash($this->getPassword(), PASSWORD_BCRYPT, ['cost' => $cost]);
	}

	public function verifyPasswordHash(string $verify_hash) : bool
	{
		return password_verify($this->getPassword(), $verify_hash);
	}

	public function reset() : void
	{
		$this->password = '';
	}

	public function toArray() : array
	{
		return [
			'password' => $this->getPassword()
		];
	}

	public function toPublicArray() : array
	{
		throw new BadMethodCallException('toPublicArray not implemented');
	}
}
