<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

define('PATH_ROOT', dirname(__DIR__));
define('PATH_PUBLIC', PATH_ROOT . '/public');
define('PATH_SRC', PATH_ROOT . '/src');
define('PATH_VENDOR', PATH_ROOT . '/vendor');
define('PATH_VIEWS', PATH_ROOT . '/views');

require_once PATH_VENDOR . '/autoload.php';

ini_set('display_errors', 1);
error_reporting(-1);

ErrorHandler::register();

if ('cli' !== php_sapi_name()) {
    ExceptionHandler::register();
}

$app = new Silex\Application();
$app['debug'] = true;

require PATH_SRC . '/configuration.php';
require PATH_SRC . '/startup.php';

$app->run();
