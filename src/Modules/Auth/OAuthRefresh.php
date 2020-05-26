<?php

namespace Modules\Auth;

use Core\Entity\CacheEntity;

class OAuthBearer extends CacheEntity
{
	public function getModelPath() : string
	{
		return '\Modules\Auth\Models\OAuthRefreshModel';
	}

	public function getCollectionTable() : string
	{
		return 'oauth_refresh';
	}

	public function getCollectionPrimaryKey() : string
	{
		return 'refresh_token';
	}

	public function getEntityCacheTime() : int
	{
		return ONE_DAY;
	}
}