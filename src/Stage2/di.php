<?php

declare(strict_types=1);

namespace App\Stage2;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $di): void {
    $di->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()

        ->set(Controller::class)
            ->public()
            ->arg('$apiClient', new Reference('introvert.api_client'))
    ;
};