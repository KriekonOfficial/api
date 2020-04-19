<?php

define('MYSQL',
	[
		'kriekon' => [
		    'host' => '192.168.33.1',
	        //'host' => '149.28.54.227',
		    'user' => 'genericuser',
	        //'user' => 'vinnie200111',
		    'password' => 'Password1234',
		    'dbname' => 'kriekon',
		    'charset' => 'UTF8'
		],
		'kriekon_log' => [
			'host' => '192.168.33.1',
	        //'host' => '149.28.54.227',
		    'user' => 'genericuser',
	        //'user' => 'vinnie200111',
		    'password' => 'Password1234',
		    'dbname' => 'kriekon_log',
		    'charset' => 'UTF8'
		]
	]
);
define('DEVENV', true);