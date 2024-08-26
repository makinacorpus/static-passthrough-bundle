<?php

declare(strict_types=1);

namespace MakinaCorpus\StaticPassthroughBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('static_passthrough');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('definitions')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('root_folder')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('path_prefix')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
