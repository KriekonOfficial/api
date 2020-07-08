<?php

require('../Core/Application.php');
Application::autoload();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/*$mail = new MailWrapper('noreply@kriekon.com', EMAILS['noreply@kriekon.com'], 'mail.kriekon.com', true);
$mail->addAddress('xnjxdanger0us@gmail.com', SITE_NAME);
$mail->send('test', 'hello world', true);*/

$username = 'noreply@kriekon.com';
$password = EMAILS['noreply@kriekon.com'];

$mail = new PHPMailer(true);
$mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;

$mail->isSMTP();
$mail->Host = $host;
$mail->SMTPAuth = true;
$mail->Username = $username;
$mail->Password = $password;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->SMTPOptions = [
'ssl' => array(
	'verify_peer' => false,
	'verify_peer_name' => false,
	'allow_self_signed' => true
)];

$mail->setFrom($username);

$mail->addAddress('xnjxdanger0us@gmail.com');

$mail->Subject = 'test1234';
$mail->Body = 'mail from prod';

$mail->send();
