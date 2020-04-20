<?php

namespace Modules\Account;

use Core\Entity\Entity;

class Account extends Entity
{
	protected string $model_path = '\Modules\Account\Models\AccountModel';

	protected string $db_table = 'account';
	protected string $db_primary_key = 'ACCTID';
}