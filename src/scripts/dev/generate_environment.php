<?php

require(__DIR__.'/../../Core/Application.php');

Application::autoload();

use Core\Environment\EnvironmentGenerator;
use Core\Environment\Environment;
use Core\Environment\Model\DatabaseModel;
use Core\Util\KeyGenerator;

$generator = new EnvironmentGenerator(Environment::DEV, '/home/kriekon/api/config.json');
$generator->addDatabase(new DatabaseModel('kriekon', '192.168.1.154', 'genericuser', 'Password1234'));
$generator->addDatabase(new DatabaseModel('kriekon_log', '192.168.1.154', 'genericuser', 'Password1234'));

$generator->addEncryptionKey('oauth_encryption', KeyGenerator::generateToken(45));
$generator->addConfigOption('emails', array(
	'noreply@kriekon.com' => '{JPyaU?fm2K2=HYz'
));
$generator->addConfigOption('default_email', 'noreply@kriekon.com');
$generator->addConfigOption('default_db', 'kriekon');
$generator->addConfigOption('default_log_db', 'kriekon_log');
$generator->addConfigOption('site_name', 'Kriekon');
$generator->addConfigOption('www_url', 'http://local.www.kriekon.com');
$generator->addConfigOption('api_url', 'http://local.api.kriekon.com');

$generator->generate();


