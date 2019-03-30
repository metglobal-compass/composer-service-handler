<?php

namespace Metglobal\ServiceHandler\Finder;

use Metglobal\ServiceHandler\Annotation\DI;

class DependencyFinder
{
    /**
     * @var PhpClassFinder
     */
    protected $phpClassFinder;

    /**
     * @var AnnotationFinder
     */
    protected $annotationFinder;

    /**
     * DependencyFinder constructor.
     * @param PhpClassFinder $phpClassFinder
     * @param AnnotationFinder $annotationFinder
     */
    public function __construct(PhpClassFinder $phpClassFinder, AnnotationFinder $annotationFinder)
    {
        $this->phpClassFinder = $phpClassFinder;
        $this->annotationFinder = $annotationFinder;
    }

    /**
     * @param string $dir
     * @param array $exclude
     * @return DI[]
     * @throws \ReflectionException
     */
    public function find(string $dir, $exclude = [])
    {
        $annotations = [];

        $classes = $this->phpClassFinder->find($dir);

        $excludedFolders = $this->parseExclude($exclude);

        foreach ($classes as $class) {
            if ($excludedFolders && $this->startsWith($class, $excludedFolders)) {
                continue;
            }
            
            $annotation = $this->annotationFinder->findDiAnnotation($class);
            if ($annotation) {
                $annotations[$annotation->id] = $annotation;
            }
        }

        return $annotations;
    }
    
    private function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }

    private function parseExclude($exclude)
    {
        $result = [];

        foreach ($exclude as $parent => $child) {
            if (is_array($child)) {
                foreach ($child as $item) {
                    $class = $parent.'/'.$item;

                    array_push($result, str_replace('/', '\\', $class));
                }

                continue;
            }

            $class = $parent.'/'.$child;

            array_push($result, str_replace('/', '\\', $class));
        }

        return $result;
    }
}
