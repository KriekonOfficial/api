<?php

namespace Controllers\Root\Test;

class Index extends \Core\Controller
{
	public function index()
	{
		/*$object = new \Modules\Account\Account();
		$model = $object->find(1);

		\Core\Response\Dump::var($model->createEntity());*/

		$model = new \Modules\Account\Models\AccountModel();
		$model->setEmail('vinnie.marone35@gmail.com');
		$model->setFirstName('Vinnie');
		$model->setLastName('Marone');
		$model->setDateOfBirth('1996-04-12');
		$model->setRegistrationTime(date(DATEFORMAT_STANDARD));
		$model->setPasswordHash(password_hash('password1234', PASSWORD_BCRYPT, array('cost' => 12)));

		$validator = new \Modules\Account\Models\AccountValidator($model);
		$validator->addValidator('minLength', [10]);
		$validator->addRule('minLength', ['email']);
		//$validator->addRule('validateEmail', ['email']);
		$validator->validate();

		//\Core\Response\Dump::var($validator);

		return new \Core\Response\ErrorResponse(500, 'test1234');
	}
}