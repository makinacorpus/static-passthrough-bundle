<?php

use MakinaCorpus\StaticPassthroughBundle\Controller\StaticPassthroughController;
use MakinaCorpus\StaticPassthroughBundle\Routing\StaticPassthroughRouteLoader;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()      // Automatically injects dependencies in your services.
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    $services
        ->set(StaticPassthroughController::class, StaticPassthroughController::class)
        ->private()
        ->tag('controller.service_arguments')
    ;

    $services
        ->set(StaticPassthroughRouteLoader::class, StaticPassthroughRouteLoader::class)
        ->arg('$projectDir', '%kernel.project_dir%')
        ->arg('$definitions', '%static_passthrough.definitions%')
    ;
};
