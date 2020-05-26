<?php

namespace Modules\Auth\Models;

use Core\Model\Model;

class OAuthRefreshModel extends Model
{
	private string $_refresh_token = '';
	private int $_ACCTID = 0;
	private string $_date_expiration = '0000-00-00 00:00:00';
	private string $_authorized_ip = '127.0.0.1';
	private int $_scope = ScopeModel::GLOBAL_SCOPE;

	protected string $refresh_token;
	protected int $ACCITD;
	protected string $date_expiration;
	protected string $authorized_ip;
	protected int $scope;

	public function getRefreshToken() : string
	{
		return $this->refresh_token;
	}

	public function setRefreshToken(string $refresh_token) : void
	{
		$this->refresh_token = $refresh_token;
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
		return $this->getRefreshToken();
	}

	public function setPrimaryKey(string $refresh_token) : void
	{
		$this->setRefreshToken($refresh_token);
	}

	public function toArray() : array
	{
		return [
			'refresh_token'   => $this->getRefreshToken(),
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
		$this->setRefreshToken($this->_refresh_token);
		$this->setACCTID($this->_ACCTID);
		$this->setDateExpiration($this->_date_expiration);
		$this->setAuthorizedIP($this->_authorized_ip);
		$this->setScope($this->_scope);
	}

	protected function getEntityPath() : string
	{
		return '\Modules\Auth\OAuthRefresh';
	}
}