<?php

namespace Modules\Account;

use Core\Entity\DBEntity;
use Modules\Account\Models\AccountModel;
use Core\Store\Database\Util\DBWrapper;
use Core\Store\Database\Exception\DatabaseException;

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
		$results = DBWrapper::PResult('
			SELECT * FROM ' . $this->getCollectionTable() . ' WHERE email = ?', [$email], $this->getCollectionName());

		if ($results->count() > 1)
		{
			throw new DatabaseException('Error found more than 1 email address in the database. Email: ' . $email);
		}

		$this->setModelProperties($results);

		return $this->getModel();
	}
}