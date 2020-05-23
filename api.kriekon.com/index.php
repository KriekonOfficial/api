<?php

define('ROOT_PATH', dirname(dirname(__FILE__)));

require(ROOT_PATH . '/src/includes/config.php');
require(ROOT_PATH . '/src/includes/global_constants.php');
require(ROOT_PATH . '/src/includes/global_functions.php');
require(ROOT_PATH . '/src/Core/Application.php');

Application::autoload();

Application::shutdown_handler();

Application::exception_handler();

Application::run();

exit();

