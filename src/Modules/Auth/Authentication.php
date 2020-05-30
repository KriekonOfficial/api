<?php

namespace Modules\Auth;

use Core\ErrorBase;
use Core\Router\Interfaces\AuthInterface;
use Core\Router\CurrentRoute;
use Modules\Auth\OAuthBearer;
use Modules\Auth\Models\OAuthBearerModel;

class Authentication extends ErrorBase implements AuthInterface
{
	public function checkAuth(CurrentRoute $route) : bool
	{
		$request = $route->getRequest();
		$server = $request::getServer();

		if (!$server->hasHeader('AUTHORIZATION'))
		{
			$this->addError('Failed to pass in authorization header, please submit your request with the appropriate token.');
			return false;
		}

		$authorization = $server->getHeader('AUTHORIZATION')[0] ?? '';

		if (!$this->parseAuthorizationHeader($authorization, $matches))
		{
			return false;
		}

		if (!$this->checkTokenType($matches['token_type']))
		{
			return false;
		}

		if (!$this->getOAuthBearer($matches['access_token'], $oauth))
		{
			$this->addError('Token does not exist.');
			return false;
		}

		if (!$this->checkDecryptedData($oauth, $matches['encrypted_string']))
		{
			return false;
		}

		if ($request::getRequestIP() != $oauth->getAuthorizedIP())
		{
			$this->addError('IP address is not authorized for that token.');
			return false;
		}

		return true;
	}

	private function parseAuthorizationHeader(string $authorization, ?array &$contents) : bool
	{
		$contents = [
			'token_type' => 'unknown',
			'base64_encrypted_info' => '',
			'encrypted_string' => '',
			'access_token' => 'unknown'
		];

		if (!preg_match('/(\S*)\s(\S*)\.(\S*)\.(\S*)/', $authorization, $matches))
		{
			$this->addError('Invalid Token specified.');
			return false;
		}

		$contents = [
			'token_type' => $matches[1] ?? 'unknown',
			'base64_encrypted_info' => $matches[2] ?? '',
			'encrypted_string' => $matches[3] ?? '',
			'access_token' => $matches[4] ?? 'unknown'
		];

		return true;
	}
	private function checkTokenType(string $token_type) : bool
	{
		if (!in_array(ucwords($token_type), ['Bearer']))
		{
			$this->addError('Unsupported token type: ' . $token_type);
			return false;
		}
		return true;
	}

	private function getOAuthBearer(string $access_token, ?OAuthBearerModel &$model = null)
	{
		$oauth = new OAuthBearer();

		$model = $oauth->find($access_token);

		return $model->isInitialized();
	}

	private function checkDecryptedData(OAuthBearerModel $model, string $encrypted_string) : bool
	{
		$data = $model->decryptData($encrypted_string);
		if ($data === null)
		{
			/**
			* Fuck em, if this happens someone fucking with my encrypted string.
			* And for that I say fuck you.
			*/
			$this->addError('Something went wrong, please login again to remedy this issue.');
			return false;
		}
		$ACCTID = $data['ACCTID'] ?? 0;

		if ($ACCTID != $model->getACCTID())
		{
			$this->addError('Hey Pal, do you want a job? Congrats on cracking the encryption. But you won\'t be able to steal that account that way :P.');
			return false;
		}

		if (time() > strtotime($model->getDateExpiration()))
		{
			$entity = new OAuthBearer($model);
			$entity->delete();
			$this->addError('Token has expired please login again.');
			return false;
		}

		return true;
	}
}