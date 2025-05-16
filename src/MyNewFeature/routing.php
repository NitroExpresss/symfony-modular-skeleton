<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routing): void {
    $routing->add('index', '/')
        ->controller('App\\MyNewFeature\\Controller::showTime');
};