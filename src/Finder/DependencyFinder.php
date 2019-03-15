<?php

namespace SymfonyAutoDiYml\Finder;

use SymfonyAutoDiYml\Annotation\DI;

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
     * @param mixed $exclude
     * @return DI[]
     * @throws \ReflectionException
     */
    public function find(string $dir, $exclude = null)
    {
        $annotations = [];

        $classes = $this->phpClassFinder->find($dir);

        $excludedFolders = $this->parseExclude($exclude);

        foreach ($classes as $class) {
            if ($this->startsWith($class, $excludedFolders)) {
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
        return explode(',', str_replace('/', '\\', $exclude));
    }
}
