<?php

namespace Controllers;

use Core\Controller;
use Core\Request\Request;
use Core\Response\ErrorResponse;
use Core\Response\SuccessResponse;
use Core\Environment\Config;

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

		if ($authorization != trim(file_get_contents(ROOT_PATH . '/../secret_keys/tworiverstax_api_key')))
		{
			return new ErrorResponse(500, 'Invalid request.');
		}

		$input = $request->getRequestInput();

		$mail = new MailWrapper(MailWrapper::getDefaultEmail(), MailWrapper::getDefaultPassword());
		$mail->addAddress(base64_decode($input->get('email'), true), SITE_NAME);
		$mail->isHTML(true);
		$mail->send(base64_decode($input->get('subject'), true), base64_decode($input->get('message'), true));

		return new SuccessResponse(200, [], 'Mail sent');
	}
}
