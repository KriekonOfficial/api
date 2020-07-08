<?php

namespace Controllers;

use Core\Controller;
use Core\Request\Request;
use Core\Response\ErrorResponse;
use Core\Response\SuccessResponse;

use Classes\MailWrapper;

class MailController extends Controller
{
	public function __construct()
	{
		$this->addRequiredAuth('send', false);
	}

	public function send(Request $request)
	{
		$server = $request::getServer();

		if (!$server->hasHeader('AUTHORIZATION'))
		{
			return new ErrorResponse(500, 'Invalid request.');
		}

		$authorization = $server->getHeader('AUTHORIZATION')[0] ?? '';

		if ($authorization != 'M5NdnJRTElAsavditOHRna798JVpDJcV')
		{
			return new ErrorResponse(500, 'Invalid request.');
		}

		$input = $request->getRequestInput();

		$mail = new MailWrapper('noreply@kriekon.com', EMAILS['noreply@kriekon.com']);
		$mail->addAddress(base64_decode($input->get('email'), true), SITE_NAME);
		$mail->send(base64_decode($input->get('subject'), true), base64_decode($input->get('message'), true));

		return new SuccessResponse(200, [], 'Mail sent');
	}
}
