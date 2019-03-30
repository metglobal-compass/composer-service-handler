<?php

namespace Metglobal\ServiceHandler\Finder;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Metglobal\ServiceHandler\Annotation\DI;
use Metglobal\ServiceHandler\Annotation\Tag;
use Metglobal\ServiceHandler\Reflector;

class AnnotationFinder
{
    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * @var Reflector
     */
    protected $reflector;

    /**
     * AnnotationFinder constructor.
     * @param AnnotationReader $annotationReader
     * @param Reflector $reflector
     */
    public function __construct(AnnotationReader $annotationReader, Reflector $reflector)
    {
        $this->annotationReader = $annotationReader;
        $this->reflector = $reflector;

        AnnotationRegistry::registerLoader('class_exists');
        AnnotationRegistry::loadAnnotationClass(DI::class);
        AnnotationRegistry::loadAnnotationClass(Tag::class);
    }

    /**
     * @param $className
     * @return null|DI
     */
    public function findDiAnnotation($className)
    {
        try {
            $reflectionClass = $this->reflector->getReflectionClass($className);

            /** @var DI $annotation */
            $annotation = $this->annotationReader->getClassAnnotation($reflectionClass, DI::class);

            // Set self class name if annotation class field is null
            if ($annotation != null && $annotation->class == null) {
                $annotation->class = $className;
            }

            return $annotation;
        } catch (\ReflectionException $e) {
            return null;
        }
    }
}
