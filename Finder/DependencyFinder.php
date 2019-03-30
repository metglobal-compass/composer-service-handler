<?php

namespace Metglobal\ServiceHandler\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DependencyFinder
{
    protected $annotationFinder;

    public function __construct(AnnotationFinder $annotationFinder)
    {
        $this->annotationFinder = $annotationFinder;
    }

    public function find(string $dir, $exclude = null): array
    {
        if (null !== $exclude) {
            list($dir, $exclude) = $this->parseExclude($exclude);
        }

        $files = $this->getFiles($dir, $exclude);

        $annotations = [];
        foreach ($files->getIterator() as $file) {
            if ($annotation = $this->annotationFinder->findServiceAnnotation($this->getClassName($file))) {
                $annotations[$annotation->id] = $annotation;
            }
        }

        return $annotations;
    }

    private function parseExclude($exclude): array
    {
        $basePath = strstr($exclude, '{', true);
        $excludedPaths = [];

        if (preg_match('/{(.*?)}/', $exclude, $matches, PREG_OFFSET_CAPTURE)) {
            $excludedPaths = explode(',', $matches[1][0]);
        }

        return [
            $basePath,
            $excludedPaths,
        ];
    }

    private function getFiles(string $dir, $exclude): Finder
    {
        $finder = new Finder();

        $files = $finder->files()->in($dir)->name('*.php');

        // Exclude directories
        if ($exclude) {
            $files->exclude($exclude);
        }

        // Exclude files
        foreach ($exclude as $excludedPath) {
            $ext = pathinfo($excludedPath, PATHINFO_EXTENSION);

            if ($ext) {
                $files->notPath($excludedPath);
            }
        }

        return $files;
    }

    private function getClassName(SplFileInfo $file): string
    {
        $className = "\\".str_replace(".php", "", $file->getFilename());

        if (preg_match("/namespace(.*);/", $file->getContents(), $matches)) {
            $className = trim($matches[1]).$className;
        }

        return $className;
    }
}
