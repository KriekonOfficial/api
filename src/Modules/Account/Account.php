<?php

namespace Modules\Account;

use Core\Entity\DBEntity;
use Modules\Account\Models\AccountModel;
use Core\Store\Database\Util\DBWrapper;

class Account extends DBEntity
{
	public function getModelPath() : string
	{
		return '\Modules\Account\Models\AccountModel';
	}

	public function getCollectionTable() : string
	{
		return 'account';
	}

	public function getCollectionPrimaryKey() : string
	{
		return 'ACCTID';
	}

	public function findEmail(string $email) : AccountModel
	{
		$count = 0;
		$results = DBWrapper::PResult('
			SELECT * FROM ' . $this->getCollectionTable() . ' WHERE email = ?', [$email], $this->getCollectionName());

		$this->setModelProperties($results);

		return $this->getModel();
	}
}