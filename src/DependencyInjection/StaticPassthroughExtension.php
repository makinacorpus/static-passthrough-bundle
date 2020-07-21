<?php

declare(strict_types=1);

namespace MakinaCorpus\StaticPassthroughBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\ProtectedPhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class StaticPassthroughExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $container->setParameter('static_passthrough.definitions', $mergedConfig['definitions'] ?? []);

        $loader = new ProtectedPhpFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.php');
    }
}
