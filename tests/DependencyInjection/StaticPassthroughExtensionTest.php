<?php

declare(strict_types=1);

namespace MakinaCorpus\StaticPassthroughBundle\Tests\DependencyInjection;

use MakinaCorpus\StaticPassthroughBundle\Controller\StaticPassthroughController;
use MakinaCorpus\StaticPassthroughBundle\DependencyInjection\StaticPassthroughExtension;
use MakinaCorpus\StaticPassthroughBundle\Routing\StaticPassthroughRouteLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class StaticPassthroughExtensionTest extends TestCase
{
    public function testCorrectConfiguration()
    {
        $configs = (new Parser())->parse(<<<EOF
        static_passthrough:
            definitions:
                foo1:
                    root_folder: 'bar/baz/foo1'
                    path_prefix: 'bar/foo1'
                foo2:
                    root_folder: 'bar/baz/foo2'
                    path_prefix: 'bar/foo2'
        EOF);

        $loader = new StaticPassthroughExtension();

        $loader->load($configs, $container = new ContainerBuilder());

        $param = $container->getParameter('static_passthrough.definitions');

        self::assertSame(
            [
                'foo1' => [
                    'root_folder' => 'bar/baz/foo1',
                    'path_prefix' => 'bar/foo1',
                ],
                'foo2' => [
                    'root_folder' => 'bar/baz/foo2',
                    'path_prefix' => 'bar/foo2',
                ],
            ],
            $param
        );

        self::assertTrue($container->hasDefinition(StaticPassthroughController::class));
        self::assertTrue($container->hasDefinition(StaticPassthroughRouteLoader::class));
    }

    public function testConfigurationWithoutRootFolderShouldFail()
    {
        $configs = (new Parser())->parse(<<<EOF
        static_passthrough:
            definitions:
                foo1:
                    path_prefix: 'bar/foo1'
                foo2:
                    root_folder: 'bar/baz/foo2'
                    path_prefix: 'bar/foo2'
        EOF);

        $loader = new StaticPassthroughExtension();

        $this->expectException(InvalidConfigurationException::class);

        $loader->load($configs, new ContainerBuilder());
    }

    public function testConfigurationWithoutPathPrefixShouldFail()
    {
        $configs = (new Parser())->parse(<<<EOF
        static_passthrough:
            definitions:
                foo1:
                    root_folder: 'bar/baz/foo1'
                foo2:
                    root_folder: 'bar/baz/foo2'
                    path_prefix: 'bar/foo2'
        EOF);

        $loader = new StaticPassthroughExtension();

        $this->expectException(InvalidConfigurationException::class);

        $loader->load($configs, new ContainerBuilder());
    }
}
