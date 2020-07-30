<?php

namespace Modules\User;

use Core\Entity\CacheEntity;
use Modules\User\Models\VerificationModel;

class UserVerification extends CacheEntity
{
	public function getModelPath() : string
	{
		return '\Modules\User\Models\VerificationModel';
	}

	public function getCollectionTable() : string
	{
		return 'user_verification';
	}

	public function getCollectionPrimaryKey() : string
	{
		return 'verification_code';
	}

	public function getEntityCacheTime() : int
	{
		return ONE_DAY * 2;
	}
}
