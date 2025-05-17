<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use App\Stage5\Controller;

return static function (RoutingConfigurator $routing): void {
    $routing->add('stage5', '/stage5')
        ->controller([Controller::class, 'index'])
        ->methods(['POST', 'GET']);
    $routing->add('stage5_yadro', '/stage5/yadro')
        ->controller([Controller::class, 'callYadro'])
        ->methods(['POST']);
};
