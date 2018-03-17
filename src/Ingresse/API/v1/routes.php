<?php

$api = $container['app'];

$api->pipe(\Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware::class);

$routeDefault = '/api/v1';

$routeUser = $routeDefault.'/users';

$api->get($routeUser, [
    \Ingresse\API\v1\Middleware\Users\GetAll::class,
    \Ingresse\API\v1\Middleware\Response::class
]);

$api->get($routeUser.'/{id}', [
    \Ingresse\API\v1\Middleware\Users\Get::class,
    \Ingresse\API\v1\Middleware\Response::class
]);

$api->post($routeUser, [
    \Ingresse\API\v1\Middleware\Users\Save::class,
    \Ingresse\API\v1\Middleware\Response::class
]);

$api->put($routeUser.'/{id}', [
    \Ingresse\API\v1\Middleware\Users\Put::class,
    \Ingresse\API\v1\Middleware\Response::class
]);

$api->delete($routeUser.'/{id}', [
    \Ingresse\API\v1\Middleware\Users\Delete::class,
    \Ingresse\API\v1\Middleware\Response::class
]);


return $api;
