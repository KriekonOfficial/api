<?php

require('../Core/Application.php');

Application::autoload();
//Application::shutdown_handler();
//Application::exception_handler();

use \Modules\Auth\ScriptAuth;
Application::bootstrapScript(ROOT_PATH . '/config.json', new ScriptAuth());
