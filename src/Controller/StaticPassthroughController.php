<?php

declare(strict_types=1);

namespace MakinaCorpus\StaticPassthroughBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StaticPassthroughController extends AbstractController
{
    public function passthrough(Request $request, string $projectDir, string $rootFolder, string $path): Response
    {

        $filename = \sprintf(
            "%s/%s/%s",
            $projectDir,
            $rootFolder,
            $path
        );

        $explodePath = \explode('/', $filename);
        $lastSlug = \array_pop($explodePath);

        // If we have a path which not end by a file name,
        // we redirect to a corresponding hypothetical .html file
        // or index.html if $path is empty
        if (\count(\explode('.', $lastSlug)) <= 1 ) {
            return $this->redirectToHtml($request, $filename);
        }

        $explodedSlug = \explode('.', $lastSlug);
        $extension = \end($explodedSlug);
        if (
            // !\in_array($extension, $directory_config['allowed_extension']) ||
            !\file_exists($filename)
        ) {
            throw new NotFoundHttpException(\sprintf("Le fichier '%s' n'a pas pu être trouvé", $filename));
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
                'Content-Type' => $this->getMimeType($extension),
                'Content-Length' => \fstat($resource)['size'],
            ]
        );
    }


    private function redirectToHtml(Request $request, string $filename): RedirectResponse
    {
        $filename = \rtrim($filename, '/');

        $suffix = '.html';
        // If file doesn't exist, we check if we have a index.html file instead
        if (!\file_exists($filename . $suffix)) {
            $suffix = '/index.html';

            if (!\file_exists($filename . $suffix)) {
                throw new NotFoundHttpException(\sprintf("Le fichier '%s' n'a pas pu être trouvé", $filename));
            }
        }

        return $this->redirectToRoute(
            $request->get('_route'),
            ['path' => \rtrim($request->get('path'), '/') . $suffix]
        );
    }

    private function getMimeType($extension) : string
    {
        switch ($extension) {
            case "css":
                return "text/css";
            case "js":
                return "application/javascript";
            case "html":
                return "text/html";

            // Images
            case "gif":
                return "image/gif";
            case "png":
                return "image/png";
            case "jpeg":
                return "image/jpg";
            case "jpg":
                return "image/jpg";
            case "svg":
                return "image/svg+xml";

            // Documents
            case "pdf":
                return "application/pdf";
            case "docx":
                return "application/msword";
            case "doc":
                return "application/msword";
            case "xls":
                return "application/vnd.ms-excel";
            case "xlsx":
                return "application/vnd.ms-excel";
            case "ppt":
                return "application/vnd.ms-powerpoint";
            case "pptx":
                return "application/vnd.ms-powerpoint";

            // Fonts
            case "ttf":
                return "application/x-font-ttf";
            case "otf":
                return "application/x-font-opentype";
            case "woff":
                return "application/font-woff";
            case "woff2":
                return "application/font-woff2";
            case "eot":
                return "application/vnd.ms-fontobject";
            case "sfnt":
                return "application/font-sfnt";
            default:
                throw new \InvalidArgumentException(\sprintf(
                    "L'extension %s n'est pas prise en charge pas le module statics_passthrough",
                    $extension
                ));
        }
    }
}