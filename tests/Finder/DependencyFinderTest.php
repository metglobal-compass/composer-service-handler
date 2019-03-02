<?php

namespace Metglobal\ServiceHandler\Tests\Finder;

use Metglobal\ServiceHandler\Annotation\DI;
use Metglobal\ServiceHandler\Finder\AnnotationFinder;
use Metglobal\ServiceHandler\Finder\DependencyFinder;
use Metglobal\ServiceHandler\Finder\PhpClassFinder;
use Metglobal\ServiceHandler\Tests\BaseTestCase;

class DependencyFinderTest extends BaseTestCase
{
    /**
     * All of classes has annotations
     *
     * @throws \ReflectionException
     */
    public function testSuccess()
    {
        $phpClassFinderMock = $this->getPhpClassFinderMock("testDir", ["TestClass", "Test2Class"]);
        $annotationFinderMock = $this->getAnnotationFinderMock(
            [
                'TestClass' => $this->getSampleDI("di1"),
                'Test2Class' => $this->getSampleDI('di2'),
            ]
        );

        $dependencyFinder = new DependencyFinder($phpClassFinderMock, $annotationFinderMock);
        $annotations = $dependencyFinder->find("testDir");

        $this->assertEquals(2, count($annotations));
    }

    /**
     * Some of class has annotations
     *
     * @throws \ReflectionException
     */
    public function testPartialSuccess()
    {
        $phpClassFinderMock = $this->getPhpClassFinderMock("testDir", ["TestClass", "Test2Class"]);
        $annotationFinderMock = $this->getAnnotationFinderMock(
            [
                'TestClass' => $this->getSampleDI('di1'),
                'Test2Class' => null,
            ]
        );

        $dependencyFinder = new DependencyFinder($phpClassFinderMock, $annotationFinderMock);
        $annotations = $dependencyFinder->find("testDir");

        $this->assertEquals(1, count($annotations));
    }

    /**
     * @param $dir
     * @param $returningClasses
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getPhpClassFinderMock($dir, $returningClasses)
    {
        $phpClassFinderMock = $this
            ->getMockBuilder(PhpClassFinder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $phpClassFinderMock
            ->expects($this->exactly(1))
            ->method('find')
            ->with($dir)
            ->willReturn($returningClasses);

        return $phpClassFinderMock;
    }

    /**
     * @param $parameterMap
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAnnotationFinderMock($parameterMap)
    {
        $annotationFinderMock = $this
            ->getMockBuilder(AnnotationFinder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $i = 0;
        foreach ($parameterMap as $parameter => $returning) {
            $annotationFinderMock
                ->expects($this->at($i))
                ->method("findDiAnnotation")
                ->with($parameter)
                ->willReturn($returning);

            $i++;
        }

        return $annotationFinderMock;
    }

    /**
     * @param $id
     * @return DI
     */
    protected function getSampleDI($id)
    {
        $di = new DI();
        $di->id = $id;
        return $di;
    }
}
