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

		$pass_model = new \Modules\Account\Models\PasswordModel('password1234');

		$pass_validator = new \Modules\Account\Models\PasswordValidator($pass_model);
		$pass_validator->addValidator('minLength', [8]);
		$pass_validator->addValidator('maxLength', [42]);
		$pass_validator->addRule('minLength', ['password']);
		$pass_validator->addRule('maxLength', ['password']);

		$pass_validator->validate();

		\Core\Response\Dump::var($pass_validator);
		$model->setPasswordHash($pass_model->generatePasswordHash());

		$validator = new \Modules\Account\Models\AccountValidator($model);
		$validator->addValidator('minLength', [30]);
		$validator->addRule('minLength', ['email']);
		$validator->addRule('validateEmail', ['email']);
		$validator->validate();

		\Core\Response\Dump::var($validator);

		return new \Core\Response\ErrorResponse(500, 'test1234');
	}
}