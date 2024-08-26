<?php

declare(strict_types=1);

namespace MakinaCorpus\StaticPassthroughBundle\DependencyInjection;

use MakinaCorpus\StaticPassthroughBundle\Controller\StaticPassthroughController;
use MakinaCorpus\StaticPassthroughBundle\Routing\StaticPassthroughRouteLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class StaticPassthroughExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        // Set Static Passthrough parameter in container
        $container->setParameter('static_passthrough.definitions', $mergedConfig['definitions'] ?? []);

        // Define StaticPassthroughController service
        $definition = new Definition();
        $definition->setClass(StaticPassthroughController::class);
        $definition->addMethodCall('setContainer', [new Reference('service_container')]);
        $definition->addTag('controller.service_arguments');

        $container->setDefinition(StaticPassthroughController::class, $definition);

        // Define StaticPassthroughRouteLoader service
        $definition = new Definition();
        $definition->setClass(StaticPassthroughRouteLoader::class);
        $definition->setArguments([
            '$projectDir' => '%kernel.project_dir%',
            '$definitions' => '%static_passthrough.definitions%',
        ]);
        // This tag is supposed to be added automatically by framework bundle
        // using auto-detection on RouteLoaderInterface interface, but I think
        // that the auto-registration will only work on statically loaded stuff
        // (from YAML files) because it's probably done too soon, those services
        // are not yet in the container. It works until 5.0.8, it broke after
        // upgrade to 5.0.11.
        $definition->addTag('routing.route_loader');

        $container->setDefinition(StaticPassthroughRouteLoader::class, $definition);
    }
}
