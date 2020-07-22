<?php

declare(strict_types=1);

namespace MakinaCorpus\StaticPassthroughBundle\Routing;

use MakinaCorpus\StaticPassthroughBundle\Controller\StaticPassthroughController;
use Symfony\Bundle\FrameworkBundle\Routing\RouteLoaderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class StaticPassthroughRouteLoader implements RouteLoaderInterface
{
    private string $projectDir;
    private array $definitions;

    public function __construct(string $projectDir, array $definitions)
    {
        $this->projectDir = $projectDir;
        $this->definitions = $definitions;
    }

    /**
     * {@inheritdoc}
     */
    public function loadRoutes(): RouteCollection
    {
        $ret = new RouteCollection();

        foreach ($this->definitions as $name => $definition) {

            if (!\array_key_exists('path_prefix', $definition)) {
                throw new \InvalidArgumentException("Static Pathrough definition should have a 'path_prefix' field, check your `config/package/static_pathrough.yml` file.");
            }
            if (!\array_key_exists('root_folder', $definition)) {
                throw new \InvalidArgumentException("Static Pathrough definition should have a 'root_folder' field, check your `config/package/static_pathrough.yml` file.");
            }

            $ret->add(
                \sprintf('static_passthrough_%s', $name),
                new Route(
                    \sprintf('/%s{path}', $definition['path_prefix']),
                    [
                        '_controller' => \sprintf('%s::%s', StaticPassthroughController::class, 'passthrough'),
                        'path' => '/',
                        'projectDir' => $this->projectDir,
                        'rootFolder' => $definition['root_folder'] ,
                    ],
                    [
                        'path' => '.+',
                    ]
                )
            );
        }

        return $ret;
    }
}
