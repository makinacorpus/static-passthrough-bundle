<?php

declare(strict_types=1);

namespace MakinaCorpus\StaticPassthroughBundle\DependencyInjection;

use MakinaCorpus\StaticPassthroughBundle\Controller\StaticPassthroughController;
use MakinaCorpus\StaticPassthroughBundle\Routing\StaticPassthroughRouteLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\ProtectedPhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class StaticPassthroughExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        // Set Static Passthrough parameter in container
        $container->setParameter('static_passthrough.definitions', $mergedConfig['definitions'] ?? []);

        // Define StaticPassthroughController service
        $definition = new Definition();
        $definition->setClass(StaticPassthroughController::class);
        $definition->setTags(['controller.service_arguments']);
        $definition->setPrivate(true);

        $container->setDefinition(StaticPassthroughController::class, $definition);

        // Define StaticPassthroughRouteLoader service
        $definition = new Definition();
        $definition->setClass(StaticPassthroughRouteLoader::class);
        $definition->setArguments([
            '$projectDir' => '%kernel.project_dir%',
            '$definitions' => '%static_passthrough.definitions%',
        ]);
        $definition->setPrivate(true);

        $container->setDefinition(StaticPassthroughRouteLoader::class, $definition);
    }
}
