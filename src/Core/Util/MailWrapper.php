<?php

namespace Core\Util;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Core\APIError;
use Core\Response\Dump;

class MailWrapper
{
	private array $addresses = [];

	private $mailer;
	public function __construct(string $username, string $password, string $host = 'mail.kriekon.com', bool $debug = false)
	{
		$mail = new PHPMailer(true);
		if ($debug === true)
		{
			$mail->SMTPDebug = SMTP::DEBUG_SERVER;
		}

		$mail->isSMTP();
		$mail->Host = $host;
		$mail->SMTPAuth = true;
		$mail->Username = $username;
		$mail->Password = $password;
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		$mail->Port = 587;
		$mail->SMTPOptions = [
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			]
		];

		$mail->setFrom($username);

		$this->mailer = $mail;
	}

	public function addAddress(string $email) : void
	{
		$this->addresses[] = $email;
	}

	public function getAddresses() : array
	{
		return $this->addresses;
	}

	public function getMailer() : PHPMailer
	{
		return $this->mailer;
	}

	public function send(string $subject, string $body) : void
	{
		$mail = $this->getMailer();
		try
		{
			foreach ($this->getAddresses() as $address)
			{
				$mail->addAddress($address);
			}
			$mail->Subject = $subject;
			$mail->Body = $body;

			$mail->send();

			$this->mailer = $mail;
		}
		catch (Exception $e)
		{
			throw new APIError('Unable to send emails to ' . implode(', ', $this->getAddresses()) . ' Error: ' . $mail->ErrorInfo);
		}
	}
}