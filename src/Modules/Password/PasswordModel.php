<?php

namespace Modules\Password;

use Core\Model\Model;
use \BadMethodCallException;

class PasswordModel extends Model
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

	public function toArray() : array
	{
		return [
			'password' => $this->getPassword()
		];
	}

	public function verifyPasswordHash(string $verify_hash) : bool
	{
		return password_verify($this->getPassword(), $verify_hash);
	}

	public function setPrimaryKey($value) : void
	{
		throw new BadMethodCallException('Password Model function is not implemented');
	}

	public function getPrimaryKey()
	{
		throw new BadMethodCallException('Password Model function is not implemented');
	}

	public function reset() : void
	{
		$this->password = '';
	}

	public function toPublicArray() : array
	{
		throw new BadMethodCallException('Password Model function is not implemented');
	}

	protected function getEntityPath() : string
	{
		throw new BadMethodCallException('Password Model function is not implemented');
	}
}