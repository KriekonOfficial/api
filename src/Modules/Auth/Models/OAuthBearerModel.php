<?php

namespace Modules\Auth\Models;

use Core\Model\Model;
use Core\Util\JSONWrapper;
use Core\Util\TimeUtils;
use Core\Environment\Config;

class OAuthBearerModel extends Model
{
	private string $_access_token = '';
	private int $_USERID = 0;
	private string $_date_expiration = TimeUtils::DATE_ZERO;
	private string $_authorized_ip = '127.0.0.1';
	private int $_scope = ScopeModel::GLOBAL_SCOPE;
	private string $_iv = '';
	private string $_salt = '';
	private string $_cipher = 'AES-256-CBC';

	private string $bearer_token ='';

	protected string $access_token;
	protected int $USERID;
	protected string $date_expiration;
	protected string $authorized_ip;
	protected int $scope;

	protected string $iv;
	protected string $salt;
	protected string $cipher;

	public function getAccessToken() : string
	{
		return $this->access_token;
	}

	public function setAccessToken(string $access_token) : void
	{
		$this->access_token = $access_token;
	}

	public function getUSERID() : int
	{
		return $this->USERID;
	}

	public function setUSERID(int $USERID) : void
	{
		$this->USERID = $USERID;
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

	public function setCipher(string $cipher) : void
	{
		$this->cipher = $cipher;
	}

	public function getCipher() : string
	{
		return $this->cipher;
	}

	public function setIV(string $iv) : void
	{
		$this->iv = base64_encode($iv);
	}

	public function getIV() : string
	{
		return $this->iv;
	}

	public function getRawIV() : string
	{
		return base64_decode($this->getIV());
	}

	public function setSalt(string $salt) : void
	{
		$this->salt = base64_encode($salt);
	}

	public function getSalt() : string
	{
		return $this->salt;
	}

	public function getRawSalt() : string
	{
		return base64_decode($this->getSalt());
	}

	public function generateBearerToken() : void
	{
		$cipher = $this->getCipher();
		$salt = openssl_random_pseudo_bytes(256);
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));

		$json = JSONWrapper::json($this->toPublicArray());

		$encrypted_string = openssl_encrypt($json, $cipher, self::getEncryptionKey(), 0, $iv);

		$this->setSalt($salt);
		$this->setIV($iv);

		$method_info = [
			'cipher' => $cipher,
		];

		$bearer_token = base64_encode(JSONWrapper::json($method_info));
		$bearer_token .= '.' . $encrypted_string;
		$bearer_token .= '.' . $this->getAccessToken();

		$this->setBearerToken($bearer_token);
	}

	public function decryptData(string $encrypted_data) : ?array
	{
		$decrypt = openssl_decrypt($encrypted_data, $this->getCipher(), self::getEncryptionKey(), 0, $this->getRawIV());

		if ($decrypt === false)
		{
			return null;
		}

		return JSONWrapper::decode($decrypt);
	}

	public function setBearerToken(string $bearer_token) : void
	{
		$this->bearer_token = $bearer_token;
	}

	public function getBearerToken() : string
	{
		return $this->bearer_token;
	}

	////
	// Abstract Functions
	////

	public function getPrimaryKey() : string
	{
		return $this->getAccessToken();
	}

	public function setPrimaryKey($access_token) : void
	{
		$this->setAccessToken($access_token);
	}

	public function toArray() : array
	{
		return [
			'access_token'    => $this->getAccessToken(),
			'USERID'          => $this->getUSERID(),
			'date_expiration' => $this->getDateExpiration(),
			'authorized_ip'   => $this->getAuthorizedIP(),
			'scope'           => $this->getScope(),
			'salt'            => $this->getSalt(),
			'iv'              => $this->getIV(),
			'cipher'          => $this->getCipher()
		];
	}

	public function toPublicArray() : array
	{
		return [
			'access_token'    => $this->getAccessToken(),
			'USERID'          => $this->getUSERID(),
			'date_expiration' => $this->getDateExpiration(),
			'authorized_ip'   => $this->getAuthorizedIP(),
			'scope'           => $this->getScope()
		];
	}

	public function reset() : void
	{
		$this->setAccessToken($this->_access_token);
		$this->setUSERID($this->_USERID);
		$this->setDateExpiration($this->_date_expiration);
		$this->setAuthorizedIP($this->_authorized_ip);
		$this->setScope($this->_scope);
		$this->setIV($this->_iv);
		$this->setSalt($this->_salt);
		$this->setCipher($this->_cipher);
	}

	protected function getEntityPath() : string
	{
		return '\Modules\Auth\OAuthBearer';
	}

	private static function getEncryptionKey() : string
	{
		return Config::getConfig()->get('encryption_keys')['oauth_encryption'];
	}
}
