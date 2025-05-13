<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Introvert\ApiClient;

return static function (ContainerConfigurator $di): void {

    $di->services()
        ->set('introvert.api_client', ApiClient::class)
        ->public()
        ->autoconfigure()
        ->autowire();
    $di->import('../src/**/di.php');
};
