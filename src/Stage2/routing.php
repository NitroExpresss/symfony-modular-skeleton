<?php
declare(strict_types=1);
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use App\Stage2\Controller;
return static function (RoutingConfigurator $routing): void {
    $routing->add('stage2', '/stage2')
        ->controller([Controller::class, 'index'])
        ->methods(['GET']);
    
    $routing->add('stage2_refresh', '/stage2/refresh')
        ->controller([Controller::class, 'refreshDays'])
        ->methods(['GET']);
};