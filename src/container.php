<?php
use Xtreamwayz\Pimple\Container as Container;

$config  = require 'config/config.php';

$container = new Container;

$container['config'] = function () use ($config) {
    return $config;
};

$container['db'] = function () use ($config) {
    $db = $config['mysql'];
    return new \PDO(
        'mysql:host='.$db['host'].';dbname='.$db['database'],
        $db['user'],
        $db['password']
    );
};

$container['cache'] = function() use ($config) {
    $redis = new \Redis();
    $redis->connect($config['redis']['host']);

    $cache = new \MatthiasMullie\Scrapbook\Adapters\Redis($redis);
    return new \MatthiasMullie\Scrapbook\Psr16\SimpleCache($cache);
};

$container[\Ingresse\API\v1\Middleware\Users\GetAll::class] = function ($c) {
    return new \Ingresse\API\v1\Middleware\Users\GetAll($c['db'], $c['cache']);
};

$container[\Ingresse\API\v1\Middleware\Users\Get::class] = function ($c) {
    return new \Ingresse\API\v1\Middleware\Users\Get($c['db'], $c['cache']);
};

$container[\Ingresse\API\v1\Middleware\Users\Save::class] = function ($c) {
    return new \Ingresse\API\v1\Middleware\Users\Save($c['db'], $c['cache']);
};

$container['router'] = function ($c) {
    return new \Zend\Expressive\Router\AuraRouter();
};

$container['app'] = $container->factory(function ($c) {
    return new \Zend\Expressive\Application($c['router'], $c);
});

$container['logger'] = function ($c) {
    $logger = new \Monolog\Logger('logger');
    $logger->pushHandler(new \Monolog\Handler\ErrorLogHandler());

    return $logger;
};

$container['listenerLogger'] = function ($c) {
    return new Ingresse\Handler\MonologErrorListener($c['logger']);
};

$container[\Zend\Stratigility\Middleware\ErrorHandler::class] = function ($c) {
    $errorHandler = (new Zend\Expressive\Container\ErrorHandlerFactory)($c);

    $errorHandler->attachListener($c['listenerLogger']);

    return $errorHandler;
};

$container[\Zend\Expressive\Middleware\ErrorResponseGenerator::class] = function ($c) {
    return (new \Zend\Expressive\Container\ErrorResponseGeneratorFactory)($c);
};


return $container;