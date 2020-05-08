<?php

namespace Modules\Auth;

use Core\Entity\DBEntity;

class OAuthEntity extends DBEntity
{
	public function getModelPath() : string
	{
		return '\Modules\Auth\Models\OAuthModel';
	}

	public function getCollectionTable() : string
	{
		return 'oauth_server';
	}

	public function getCollectionPrimaryKey() : string
	{
		return 'ACCTID';
	}
}