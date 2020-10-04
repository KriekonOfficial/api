<?php

namespace Modules\Auth;

use Core\Entity\CacheEntity;
use Core\Util\TimeUtils;

class OAuthBearer extends CacheEntity
{
	public function getModelPath() : string
	{
		return '\Modules\Auth\Models\OAuthBearerModel';
	}

	public function getCollectionTable() : string
	{
		return 'oauth_bearer';
	}

	public function getCollectionPrimaryKey() : string
	{
		return 'access_token';
	}

	public function getEntityCacheTime() : int
	{
		return TimeUtils::ONE_HOUR + (TimeUtils::ONE_MIN * 10);
	}
}
