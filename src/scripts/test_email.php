<?php

require('../Core/Application.php');
Application::autoload();

$mail = new MailWrapper('noreply@kriekon.com', EMAILS['noreply@kriekon.com'], 'mail.kriekon.com', true);
$mail->addAddress('xnjxdanger0us@gmail.com', SITE_NAME);
$mail->send('test', 'hello world', true);
