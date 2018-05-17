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
     * @return DI[]
     * @throws \ReflectionException
     */
    public function find(string $dir)
    {
        $annotations = [];

        $classes = $this->phpClassFinder->find($dir);

        foreach ($classes as $class) {
            $annotation = $this->annotationFinder->findDiAnnotation($class);
            if ($annotation) {
                $annotations[$annotation->id] = $annotation;
            }
        }

        return $annotations;
    }
}
