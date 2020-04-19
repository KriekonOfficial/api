<?php

namespace Controllers\Root\Test;

class Index extends \Core\Controller
{
	public function index()
	{
		$object = new \Modules\Account\Account();
		\Core\Response\Dump::var($object->find(2));
		//\Core\Response\Dump::var($object);

		return new \Core\Response\ErrorResponse(500, 'test1234');
	}
}