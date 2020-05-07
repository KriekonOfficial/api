<?php

namespace Modules\Account;

use Core\Entity\CacheEntity;

class AccountVerification extends CacheEntity
{
	public function getModelPath() : string
	{
		return '\Modules\Account\Models\VerificationModel';
	}

	public function getCollectionTable() : string
	{
		return 'account_verification';
	}

	public function getCollectionPrimaryKey() : string
	{
		return 'ACCTID';
	}

	public function getEntityCacheTime() : int
	{
		return ONE_DAY * 2;
	}
}