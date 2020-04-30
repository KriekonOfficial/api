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
		return password_hash($this->getPassword(), PASSWORD_BCRYPT, $cost);
	}

	public function toArray() : array
	{
		return [
			'password' => $this->getPassword()
		];
	}

	public function setPrimaryKey(int $value) : void
	{
		throw new BadMethodCallException('Password Model function is not implemented');
	}

	public function getPrimaryKey() : int
	{
		throw new BadMethodCallException('Password Model function is not implemented');
	}

	public function reset() : void
	{
		throw new BadMethodCallException('Password Model function is not implemented');
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