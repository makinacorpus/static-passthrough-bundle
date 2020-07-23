<?php

use MakinaCorpus\StaticPassthroughBundle\Routing\StaticPassthroughRouteLoader;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->import(StaticPassthroughRouteLoader::class . '::loadRoutes', 'service');
};
