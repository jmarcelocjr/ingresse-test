<?php
error_reporting(0);

use Interop\Http\ServerMiddleware\DelegateInterface;


chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$container = require 'src/container.php';

$app = $container['app'];

$app->pipe('/api/v1', require_once 'src/Ingresse/API/v1/routes.php');

$app->pipe(\Zend\Stratigility\Middleware\ErrorHandler::class);

$app->pipeRoutingMiddleware();
$app->pipeDispatchMiddleware();

$app->run();