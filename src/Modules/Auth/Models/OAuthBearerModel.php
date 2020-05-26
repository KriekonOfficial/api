<?php

namespace Modules\Auth\Models;

use Core\Model\Model;

class OAuthModel extends Model
{
	private string $_access_token = '';
	private int $_ACCTID = 0;
	private string $_date_expiration = '0000-00-00 00:00:00';
	private string $_authorized_ip = '127.0.0.1';
	private int $_scope = ScopeModel::GLOBAL_SCOPE;

	protected string $access_token;
	protected int $ACCTID;
	protected string $date_expiration;
	protected string $authorized_ip;
	protected int $scope;

	public function getAccessToken() : string
	{
		return $this->access_token;
	}

	public function setAccessToken(string $access_token) : void
	{
		$this->access_token = $access_token;
	}

	public function getACCTID() : int
	{
		return $this->ACCTID;
	}

	public function setACCTID(int $ACCTID) : void
	{
		$this->ACCTID = $ACCTID
	}

	public function getDateExpiration() : string
	{
		return $this->date_expiration;
	}

	public function setDateExpiration(string $date_expiration) : void
	{
		$this->date_expiration = $date_expiration;
	}

	public function getAuthorizedIP() : string
	{
		return $this->authorized_ip;
	}

	public function setAuthorizedIP(string $ip_address) : void
	{
		$this->authorized_ip = $ip_address;
	}

	public function getScope() : int
	{
		return $this->scope;
	}

	public function setScope(int $scope) : void
	{
		$this->scope = $scope;
	}

	////
	// Abstract Functions
	////

	public function getPrimaryKey() : string
	{
		return $this->getAccessToken();
	}

	public function setPrimaryKey(string $access_token) : void
	{
		$this->setAccessToken($access_token);
	}

	public function toArray() : array
	{
		return [
			'access_token'    => $this->getAccessToken(),
			'ACCTID'          => $this->getACCTID(),
			'date_expiration' => $this->getDateExpiration(),
			'authorized_ip'   => $this->getAuthorizedIP(),
			'scope'           => $this->getScope()
		];
	}

	public function toPublicArray() : array
	{
		$array = $this->toArray();
		unset($array['authorized_ip']);
		return $array;
	}

	public function reset() : void
	{
		$this->setAccessToken($this->_access_token);
		$this->setACCTID($this->_ACCTID);
		$this->setDateExpiration($this->_date_expiration);
		$this->setAuthorizedIP($this->_authorized_ip);
		$this->setScope($this->_scope);
	}

	protected function getEntityPath() : string
	{
		return '\Modules\Auth\OAuthBearer';
	}
}