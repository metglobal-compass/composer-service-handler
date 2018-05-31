<?php

namespace SymfonyAutoDiYml\Tests\Finder;

use Doctrine\Common\Annotations\AnnotationReader;
use SymfonyAutoDiYml\Annotation\Tag;
use SymfonyAutoDiYml\Finder\AnnotationFinder;
use SymfonyAutoDiYml\Reflector;
use SymfonyAutoDiYml\Tests\Fixtures\Annotation\ComplexClass;
use SymfonyAutoDiYml\Tests\Fixtures\Annotation\ConfigClass;
use SymfonyAutoDiYml\Tests\Fixtures\Annotation\SimpleClass;
use SymfonyAutoDiYml\Tests\Fixtures\Annotation\WrongClass;
use SymfonyAutoDiYml\Tests\BaseTestCase;

/**
 * Depends fixtures class under SymfonyAutoDiYml\Tests\Annotation namespace
 *
 * @package SymfonyAutoDiYml\Tests\Finder
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
