<?php

namespace Controllers\Root\Test;

class Index extends \Core\Controller
{
	public function index()
	{
		\Core\Util\TimeUtils::getAge('1996-04-12', date(DATEFORMAT_STANDARD));

		return new \Core\Response\ErrorResponse(500, 'test1234');
	}
}