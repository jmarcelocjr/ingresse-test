<?php
use Xtreamwayz\Pimple\Container as Container;

$config  = require __DIR__ . '/config.php';

$container = new Container;

$container['config'] = function () {
    return $config;
}

$container['db'] function () {
    return new \PDO(
        'mysql:host='.$config['host'].';dbname='.$config['database'],
        $config['user'],
        $config['password']
    );
}

$container['router'] = function ($c) {
    return new \Zend\Expressive\Router\AuraRouter();
};

$container['app'] = $container->factory(function ($c) {
    return new \Zend\Expressive\Application($c['router'], $c);
});

return $container;