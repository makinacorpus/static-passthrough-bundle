<?php

declare(strict_types=1);

namespace MakinaCorpus\StaticPassthroughBundle\Tests\DependencyInjection;

use MakinaCorpus\StaticPassthroughBundle\Controller\StaticPassthroughController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StaticPassthroughControllerTest extends KernelTestCase
{
    public function testBrowsingExistingFile()
    {
        $controller = new StaticPassthroughController();

        $response = $controller->passthrough(new Request(), \dirname(__DIR__), 'Resources', 'test.html');

        self::assertSame(
            <<<EOF
            <html>
                <body>
                    <p>hello world</p>
                </body>
            </html>
            EOF,
            $response->getContent()
        );
    }

    public function testBrowsingNoneExistingFile()
    {
        $controller = new StaticPassthroughController();

        $this->expectException(NotFoundHttpException::class);
        $controller->passthrough(new Request(), \dirname(__DIR__), 'Resources', 'bar.html');
    }

    public function testBrowsingFileOutsideRootDir()
    {
        $controller = new StaticPassthroughController();

        $this->expectException(AccessDeniedHttpException::class);
        $controller->passthrough(new Request(), \dirname(__DIR__), '..', 'README.md');
    }

    public function testBrowsingFileWithoutExtension()
    {
        $controller = new StaticPassthroughController();

        $response = $controller->passthrough(new Request(['path' => 'foo/bar']), \dirname(__DIR__), 'Resources', 'foo/bar');

        self::assertSame(302, $response->getStatusCode()
        );
    }

    public function testBrowsingFolderWithIndex()
    {
        $controller = new StaticPassthroughController();

        $response = $controller->passthrough(new Request(['path' => 'foo/bar']), \dirname(__DIR__), 'Resources', 'foo');

        self::assertSame(302, $response->getStatusCode()
        );
    }
}
