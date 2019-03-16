<?php

namespace Metglobal\ServiceHandler\Finder;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Metglobal\ServiceHandler\Annotation\Service;
use Metglobal\ServiceHandler\Annotation\Tag;

class AnnotationFinder
{
    protected $annotationReader;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;

        AnnotationRegistry::registerLoader('class_exists');
        AnnotationRegistry::loadAnnotationClass(Service::class);
        AnnotationRegistry::loadAnnotationClass(Tag::class);
    }

    public function findServiceAnnotation($className)
    {
        try {
            $reflectionClass = new \ReflectionClass($className);

            /** @var Service $annotation */
            $annotation = $this->annotationReader->getClassAnnotation($reflectionClass, Service::class);

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
