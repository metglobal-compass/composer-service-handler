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

    public function find(string $bundle, string $dir, $exclude = null): array
    {
        if (null !== $exclude) {
            list($dir, $exclude) = $this->parseExclude($bundle, $exclude);
        }

        $files = $this->getFiles($dir, $exclude);

        $annotations = [];
        foreach ($files->getIterator() as $file) {
            $className = $this->getClassName($file, false);

            if ($annotation = $this->annotationFinder->findServiceAnnotation($className)) {
                $id = $annotation->id;

                // If id not defined, define self class name as id.
                if (!$id) {
                    $id = $className;
                }

                // If id not equals to class name (an alias used for id), set annotation class.
                if ($id != $className) {
                    $annotation->class = $className;
                }

                $annotations[$id] = $annotation;
            }
        }

        return $annotations;
    }

    private function parseExclude(string $bundle, $exclude): array
    {
        $basePath = strstr($exclude, '{', true);
        $excludedPaths = [];

        if (preg_match('/{(.*?)}/', $exclude, $matches, PREG_OFFSET_CAPTURE)) {
            $excludedPaths = explode(',', $matches[1][0]);

            // Add bundle name
            $excludedPaths = array_map(
                function ($excludedPath) use ($bundle) {
                    if ($ext = pathinfo($excludedPath, PATHINFO_EXTENSION)) {
                        return $bundle.$excludedPath;
                    }

                    return $bundle.$excludedPath.'\\';
                },
                $excludedPaths
            );
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

        // Service folders an files
        $files = $files->filter(
            function (SplFileInfo $file) use ($exclude) {
                $className = $this->getClassName($file, true);

                // Service folders an files
                if ($this->startsWith($className, $exclude) || $this->endsWith($className, $exclude)) {
                    return false;
                }

                return true;
            }
        );

        return $files;
    }

    private function getClassName(SplFileInfo $file, $ext = true): string
    {
        $className = '\\'.$file->getFilename();

        if (!$ext) {
            $className = '\\'.str_replace('.php', '', $file->getFilename());
        }

        if (preg_match('/namespace(.*);/', $file->getContents(), $matches)) {
            $className = trim($matches[1]).$className;
        }

        return $className;
    }

    private function startsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }

    private function endsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }
}
