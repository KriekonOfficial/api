<?php

namespace Modules\User\Models;

use Core\Model\Model;

class VerificationModel extends Model
{
	private int $_USERID = 0;
	private string $_verification_code = '';
	private string $_date_expire = '0000-00-00 00:00:00';

	protected int $USERID;
	protected string $verification_code;
	protected string $date_expire;

	public function getUSERID() : int
	{
		return $this->USERID;
	}

	public function setUSERID(int $USERID) : void
	{
		$this->USERID = $USERID;
	}

	public function getVerificationCode() : string
	{
		return $this->verification_code;
	}

	public function setVerificationCode(string $verification_code) : void
	{
		$this->verification_code = $verification_code;
	}

	public function getDateExpire() : string
	{
		return $this->date_expire;
	}

	public function setDateExpire(string $date_expire) : void
	{
		$this->date_expire = $date_expire;
	}

	////
	// Abstract
	////

	public function getPrimaryKey()
	{
		return $this->getVerificationCode();
	}

	public function setPrimaryKey($value) : void
	{
		$this->setVerificationCode($value);
	}

	public function toArray() : array
	{
		return [
			'USERID'            => $this->getUSERID(),
			'verification_code' => $this->getVerificationCode(),
			'date_expire'       => $this->getDateExpire()
		];
	}

	public function toPublicArray() : array
	{
		return $this->toArray();
	}

	public function reset() : void
	{
		$this->setUSERID($this->_USERID);
		$this->setVerificationCode($this->_verification_code);
		$this->setDateExpire($this->_date_expire);
	}

	protected function getEntityPath() : string
	{
		return '\Modules\User\UserVerification';
	}
}