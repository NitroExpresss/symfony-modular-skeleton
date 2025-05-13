<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    Introvert\Configuration::getDefaultConfiguration()->setApiKey('key', '%env(INTROVERT_YADRO_API_KEY)%');
    $di->services()
        ->set('introvert.api_client', \Introvert\ApiClient::class)
        ->public();
    $di->import('../src/**/di.php');
};
