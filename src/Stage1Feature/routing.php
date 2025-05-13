<?php
declare(strict_types=1);
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use App\Stage1Feature\Controller;
return static function (RoutingConfigurator $routing): void {
    $routing->add('stage1_index', '/stage1')
        ->controller([Controller::class, 'index'])
        ->methods(['GET', 'POST']);
};