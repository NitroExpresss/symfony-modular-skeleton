<?php

declare(strict_types=1);

namespace App\MyBusinessFeature;

use App\MyNewFeature\Controller;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
        $di->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
        ->set(Controller::class)
    ;
};


