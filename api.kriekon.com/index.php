<?php

require('../src/Core/Application.php');

Application::autoload();

Application::shutdown_handler();

//Application::error_handler();

Application::exception_handler();

use \Core\Router\RouterLib;
use \Core\Router\Router;
use \Core\Router\RouterURI;
use \Classes\Routes;
use \Modules\Auth\Authentication;

$uri = RouterLib::parseURI(RouterLib::initRoutes(new Routes()));
$router = new Router($uri, new Authentication());
$router->routeAgent();

exit();

