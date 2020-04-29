<?php

namespace Modules\Account\Models;

use Core\Model\Model;

class AccountModel extends Model
{
	public const LOCKED_ON = 1;
	public const LOCKED_OFF = 0;

	public const VERIFIED_ON = 1;
	public const VERIFIED_OFF = 0;

	private int $_ACCTID = 0;
	private string $_first_name = '';
	private string $_last_name = '';
	private string $_email = '';
	private string $_username = '';
	private string $_password_hash = '';
	private string $_date_of_birth = '0000-00-00';
	private string $_registration_time = '0000-00-00 00:00:00';
	private int $_verified = self::VERIFIED_OFF;
	private int $_locked = self::LOCKED_OFF;

	protected int $ACCTID;
	protected string $first_name;
	protected string $last_name;
	protected string $email;
	protected string $username;
	protected string $password_hash;
	protected string $date_of_birth;
	protected string $registration_time;
	protected int $verified;
	protected int $locked;

	public function getACCTID() : int
	{
		return $this->ACCTID;
	}

	public function getFirstName() : string
	{
		return $this->first_name;
	}

	public function setFirstName(string $first_name) : void
	{
		$this->first_name = $first_name;
	}

	public function getLastName() : string
	{
		return $this->last_name;
	}

	public function setLastName(string $last_name) : void
	{
		$this->last_name = $last_name;
	}

	public function getEmail() : string
	{
		return $this->email;
	}

	public function setEmail(string $email) : void
	{
		$this->email = $email;
	}

	public function getUsername() : string
	{
		return $this->username;
	}

	public function setUsername(string $username) : void
	{
		$this->username = $username;
	}

	public function getPasswordHash() : string
	{
		return $this->password_hash;
	}

	public function setPasswordHash(string $password_hash) : void
	{
		$this->password_hash = $password_hash;
	}

	public function getDateOfBirth() : string
	{
		return $this->date_of_birth;
	}

	public function setDateOfBirth(string $date_of_birth) : void
	{
		$this->date_of_birth = $date_of_birth;
	}

	public function getRegistrationTime() : string
	{
		return $this->registration_time;
	}

	public function setRegistrationTime(string $registration_time) : void
	{
		$this->registration_time = $registration_time;
	}

	public function getVerified() : int
	{
		return $this->verified;
	}

	public function setVerified(int $verified) : void
	{
		$this->verified = $verified;
	}

	public function getLocked() : int
	{
		return $this->locked;
	}

	public function setLocked(int $locked) : void
	{
		$this->locked = $locked;
	}

	public function isVerified() : bool
	{
		return $this->getVerified() === self::VERIFIED_ON;
	}

	public function isLocked() : bool
	{
		return $this->getLocked() == self::LOCKED_ON;
	}

	////
	// Abstract Functions
	////

	public function getPrimaryKey() : int
	{
		return $this->getACCTID();
	}

	public function setPrimaryKey(int $value) : void
	{
		$this->ACCTID = $value;
	}

	public function toArray() : array
	{
		return [
			'ACCTID' => $this->getACCTID(),
			'first_name' => $this->getFirstName(),
			'last_name' => $this->getLastName(),
			'email' => $this->getEmail(),
			'username' => $this->getUsername(),
			'password_hash' => $this->getPasswordHash(),
			'date_of_birth' => $this->getDateOfBirth(),
			'registration_time' => $this->getRegistrationTime(),
			'verified' => $this->getVerified(),
			'locked' => $this->getLocked()
		];
	}

	public function toPublicArray() : array
	{
		$array = $this->toArray();
		unset($array['password_hash']);
		return $array;
	}

	public function reset() : void
	{
		$this->ACCTID = $this->_ACCTID;
		$this->setFirstName($this->_first_name);
		$this->setLastName($this->_last_name);
		$this->setEmail($this->_email);
		$this->setUsername($this->_username);
		$this->setPasswordHash($this->_password_hash);
		$this->setDateOfBirth($this->_date_of_birth);
		$this->setRegistrationTime($this->_registration_time);
		$this->setVerified($this->_verified);
		$this->setLocked($this->_locked);
	}

	protected function getEntityPath() : string
	{
		return '\Modules\Account\Account';
	}
}