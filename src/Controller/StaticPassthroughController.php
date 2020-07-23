<?php

declare(strict_types=1);

namespace MakinaCorpus\StaticPassthroughBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mime\MimeTypes;

class StaticPassthroughController extends AbstractController
{
    public function passthrough(Request $request, string $projectDir, string $rootFolder, string $path): Response
    {
        $absoluteRootFolder = \sprintf("%s/%s", $projectDir, $rootFolder);
        $filename = \sprintf("%s/%s", $absoluteRootFolder, $path);

        // If $filename does not end by an extension, we redirect to an hypothetical
        // $filename . '.html' or $filename . '/index.html'
        if (!\pathinfo($filename, \PATHINFO_EXTENSION)) {
            return $this->redirectToHtml($request, $filename);
        }

        // Check if user tries to get a file outside root folder
        if (false === \strpos(\realpath($filename), $absoluteRootFolder)) {
            throw new AccessDeniedHttpException(\sprintf("Can't access '%s': it's outside '%s'", $filename, $rootFolder));
        }

        if (!\file_exists($filename)) {
            throw new NotFoundHttpException(\sprintf("'%s' file can't be found", $filename));
        }

        $resource = @\fopen($filename, 'r');

        return new StreamedResponse(
            function () use ($resource) {
                \fpassthru($resource);
                exit();
            },
            200,
            [
                'Content-Transfer-Encoding', 'binary',
                'Content-Type' => $this->getMimeType($filename),
                'Content-Length' => \fstat($resource)['size'],
            ]
        );
    }

    private function redirectToHtml(Request $request, string $filename): RedirectResponse
    {
        $filename = \rtrim($filename, '/');

        $suffix = '.html';
        // If file does not exist, we check if we have a index.html file instead
        if (!\file_exists($filename . $suffix)) {
            $suffix = '/index.html';

            if (!\file_exists($filename . $suffix)) {
                throw new NotFoundHttpException(\sprintf("'%s' file can't be found", $filename));
            }
        }

        return $this->redirectToRoute(
            $request->get('_route'),
            ['path' => \rtrim($request->get('path'), '/') . $suffix]
        );
    }

    private function getMimeType(string $filename): string
    {
        $mimeTypes = new MimeTypes();
        $extension = \pathinfo($filename, \PATHINFO_EXTENSION);

        if (\count($types = $mimeTypes->getMimeTypes($extension))) {
            return \reset($types);
        }

        return $mimeTypes->guessMimeType($filename) ?? '';
    }
}
