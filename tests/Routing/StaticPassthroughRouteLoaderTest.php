<?php

declare(strict_types=1);

namespace MakinaCorpus\StaticPassthroughBundle\Tests\Routing;

use MakinaCorpus\StaticPassthroughBundle\Routing\StaticPassthroughRouteLoader;
use PHPUnit\Framework\TestCase;

class StaticPassthroughRouteLoaderTest extends TestCase
{
    public function testRouteLoader()
    {
        $routeLoader = new StaticPassthroughRouteLoader(
            '/path/to/project',
            [
                'foo1' => [
                    'root_folder' => 'bar/baz/foo1',
                    'path_prefix' => 'bar/foo1',
                ],
                'foo2' => [
                    'root_folder' => 'bar/baz/foo2',
                    'path_prefix' => 'bar/foo2',
                ],
            ]
        );

        $routeCollection = $routeLoader->loadRoutes();

        self::assertNotNull($route = $routeCollection->get('static_passthrough_foo1'));
        self::assertSame('/bar/foo1{path}', $route->getPath());
        self::assertSame('/path/to/project', $route->getDefault('projectDir'));
        self::assertSame('bar/baz/foo1', $route->getDefault('rootFolder'));
        self::assertSame('MakinaCorpus\StaticPassthroughBundle\Controller\StaticPassthroughController::passthrough', $route->getDefault('_controller'));

        self::assertNotNull($route = $routeCollection->get('static_passthrough_foo2'));
        self::assertSame('/bar/foo2{path}', $route->getPath());
        self::assertSame('/path/to/project', $route->getDefault('projectDir'));
        self::assertSame('bar/baz/foo2', $route->getDefault('rootFolder'));
        self::assertSame('MakinaCorpus\StaticPassthroughBundle\Controller\StaticPassthroughController::passthrough', $route->getDefault('_controller'));
    }
}
