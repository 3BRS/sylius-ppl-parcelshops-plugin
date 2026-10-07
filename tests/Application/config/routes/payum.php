<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

// payum/payum-bundle < 2.7 provides its routes only as XML, which Symfony 8 cannot load
return static function (RoutingConfigurator $routes): void {
    $routing = dirname(__DIR__, 4) . '/vendor/payum/payum-bundle/Resources/config/routing/all';
    $routes->import(is_file($routing . '.yaml') ? $routing . '.yaml' : $routing . '.xml');
};
