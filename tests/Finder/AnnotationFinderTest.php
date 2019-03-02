<?php

namespace Metglobal\ServiceHandler\Tests\Finder;

use Doctrine\Common\Annotations\AnnotationReader;
use Metglobal\ServiceHandler\Annotation\Tag;
use Metglobal\ServiceHandler\Finder\AnnotationFinder;
use Metglobal\ServiceHandler\Reflector;
use Metglobal\ServiceHandler\Tests\Fixtures\Annotation\ComplexClass;
use Metglobal\ServiceHandler\Tests\Fixtures\Annotation\ConfigClass;
use Metglobal\ServiceHandler\Tests\Fixtures\Annotation\SimpleClass;
use Metglobal\ServiceHandler\Tests\Fixtures\Annotation\WrongClass;
use Metglobal\ServiceHandler\Tests\BaseTestCase;

/**
 * Depends fixtures class under Metglobal\ServiceHandler\Tests\Annotation namespace
 *
 * @package Metglobal\ServiceHandler\Tests\Finder
 */
class AnnotationFinderTest extends BaseTestCase
{
    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testSimpleCase()
    {
        $annotationFinder = new AnnotationFinder(new AnnotationReader(), new Reflector());

        $annotation = $annotationFinder->findDiAnnotation(SimpleClass::class);

        $this->assertNotNull($annotation, "Annotation not found");
        $this->assertEquals('sample_bundle.sample_class', $annotation->id);
        $this->assertEquals(SimpleClass::class, $annotation->class);
    }

    /**
     * Configuration class test case for another class
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testConfigClassCase()
    {
        $annotationFinder = new AnnotationFinder(new AnnotationReader(), new Reflector());

        $annotation = $annotationFinder->findDiAnnotation(ConfigClass::class);

        $this->assertNotNull($annotation, "Annotation not found");
        $this->assertEquals('sample_bundle.copy_sample_class', $annotation->id);
        $this->assertEquals(SimpleClass::class, $annotation->class);
    }

    /**
     * A non-sense but complex test case to ensure all cases works fine
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testComplexClassCase()
    {
        $annotationFinder = new AnnotationFinder(new AnnotationReader(), new Reflector());

        $annotation = $annotationFinder->findDiAnnotation(ComplexClass::class);

        $this->assertNotNull($annotation, "Annotation not found");
        $this->assertEquals('sample_bundle.complex_class', $annotation->id);
        $this->assertEquals(["@sample_bundle.simple_class"], $annotation->arguments);
        $this->assertEquals(["@sample_bundle.complex_class_factory", "create"], $annotation->factory);
        $this->assertEquals([['setField', ['value']], ['setAnotherField', ['anotherValue']]], $annotation->calls);
        $this->assertEquals(false, $annotation->public);
        $this->assertEquals(true, $annotation->abstract);
        $this->assertEquals(true, $annotation->lazy);
        $this->assertEquals("sample_bundle.parent_of_complex_class", $annotation->parent);

        $tag = new Tag();
        $tag->name = "tag_name";
        $tag->event = "eventName";
        $tag->method = "eventMethod";
        $tag->priority = 15;
        $this->assertEquals([$tag], $annotation->tags);
    }

    /**
     * Wrong Class has an wrong id.
     *
     * @expectedException \Doctrine\Common\Annotations\AnnotationException
     */
    public function testWrongClassCase()
    {
        $annotationFinder = new AnnotationFinder(new AnnotationReader(), new Reflector());

        $annotation = $annotationFinder->findDiAnnotation(WrongClass::class);
    }

    /**
     * Annotation finder must return null if reflection expection throws
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testReflectionExceptionCase()
    {
        $reflectorMock = $this
            ->getMockBuilder(Reflector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflectorMock
            ->expects($this->exactly(1))
            ->method("getReflectionClass")
            ->with(SimpleClass::class)
            ->willThrowException(new \ReflectionException());

        $annotationFinder = new AnnotationFinder(new AnnotationReader(), $reflectorMock);
        $annotation = $annotationFinder->findDiAnnotation(SimpleClass::class);

        $this->assertNull($annotation);
    }
}
