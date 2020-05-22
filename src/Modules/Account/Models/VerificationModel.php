<?php

namespace Modules\Account\Models;

use Core\Model\Model;

class VerificationModel extends Model
{
	private int $_ACCTID = 0;
	private string $_verification_code = '';
	private string $_date_expire = '0000-00-00 00:00:00';

	protected int $ACCTID;
	protected string $verification_code;
	protected string $date_expire;

	public function getACCTID() : int
	{
		return $this->ACCTID;
	}

	public function setACCTID(int $ACCTID) : void
	{
		$this->ACCTID = $ACCTID;
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
			'ACCTID'            => $this->getACCTID(),
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
		$this->setPrimaryKey($this->_ACCTID);
		$this->setVerificationCode($this->_verification_code);
		$this->setDateExpire($this->_date_expire);
	}

	protected function getEntityPath() : string
	{
		return '\Modules\Account\AccountVerification';
	}
}