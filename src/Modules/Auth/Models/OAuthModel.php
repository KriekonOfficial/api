<?php

namespace Modules\Auth\Models;

use Core\Model\Model;

class OAuthModel extends Model
{
	private int $_ACCTID = 0;
	private string $_access_secret = '';
	private string $_access_iv = '';
	private string $_access_tag = '';
	private string $_access_token = '';
	private string $_refresh_token = '';
	private string $_access_token_expiration = '0000-00-00 00:00:00';
	private string $_refresh_token_expiration = '0000-00-00 00:00:00';

	protected int $ACCTID;
	protected string $access_secret;
	protected string $access_iv;
	protected string $access_tag;
	protected string $access_token;
	protected string $refresh_token;
	protected string $access_token_expiration;
	protected string $refresh_token_expiration;

	public function getACCTID() : int
	{
		return $this->ACCTID;
	}

	public function getAccessSecret() : string
	{
		return $this->access_secret;
	}

	public function setAccessSecret(string $access_secret) : void
	{
		$this->access_secret = $access_secret;
	}

	public function getAccessTag() : string
	{
		return $this->access_tag;
	}

	public function setAccessTag(string $access_tag) : void
	{
		$this->access_tag = $access_tag;
	}

	public function getAccessIv() : string
	{
		return $this->access_iv;
	}

	public function setAccessIv(string $access_iv) : void
	{
		$this->access_iv = $access_iv;
	}

	public function getAccessToken() : string
	{
		return $this->access_token;
	}

	public function setAccessToken(string $access_token) : void
	{
		$this->access_token = $access_token;
	}

	public function getRefreshToken() : string
	{
		return $this->refresh_token;
	}

	public function setRefreshToken(string $refresh_token) : void
	{
		$this->refresh_token = $refresh_token;
	}

	public function getAccessTokenExpiration() : string
	{
		return $this->access_token_expiration;
	}

	public function setAccessTokenExpiration(string $date_time) : void
	{
		$this->access_token_expiration = $date_time;
	}

	public function getRefreshTokenExpiration() : string
	{
		return $this->refresh_token_expiration;
	}

	public function setRefreshTokenExpiration(string $date_time) : void
	{
		$this->refresh_token_expiration = $date_time;
	}

	////
	// Abstract Functions
	////

	public function getPrimaryKey() : int
	{
		return $this->getACCTID();
	}

	public function setPrimaryKey(int $ACCTID) : void
	{
		$this->ACCTID = $ACCTID;
	}

	public function toArray() : array
	{
		return [
			'ACCTID'                   => $this->getACCTID(),
			'access_secret'            => $this->getAccessSecret(),
			'access_iv'                => $this->getAccessIv(),
			'access_tag'               => $this->getAccessTag(),
			'access_token'             => $this->getAccessToken(),
			'refresh_token'            => $this->getRefreshToken(),
			'access_token_expiration'  => $this->getAccessTokenExpiration(),
			'refresh_token_expiration' => $this->getRefreshTokenExpiration()
		];
	}

	public function toPublicArray() : array
	{
		return [
			'ACCTID'                   => $this->getACCTID(),
			'access_token'             => $this->getAccessToken(),
			'refresh_token'            => $this->getRefreshToken(),
			'access_token_expiration'  => $this->getAccessTokenExpiration(),
			'refresh_token_expiration' => $this->getRefreshTokenExpiration()
		];
	}

	public function reset() : void
	{
		$this->setPrimaryKey($this->_ACCTID);
		$this->setAccessToken($this->_access_token);
		$this->setAccessSecret($this->_access_secret);
		$this->setAccessIv($this->_access_iv);
		$this->setAccessTag($this->_access_tag);
		$this->setAccessTokenExpiration($this->_access_token_expiration);
		$this->setRefreshTokenExpiration($this->_refresh_token_expiration);
	}

	protected function getEntityPath() : string
	{
		return '\Modules\Auth\OAuthEntity';
	}
}