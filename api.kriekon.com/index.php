<?php

require('../src/Core/Application.php');

Application::autoload();
Application::shutdown_handler();
Application::exception_handler();
//Application::error_handler();

use \Classes\Routes;
use \Modules\Auth\Authentication;

Application::bootstrapWeb(ROOT_PATH . '/config.json', new Routes(), new Authentication());

exit();
