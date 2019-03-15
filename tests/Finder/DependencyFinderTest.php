<?php

namespace Metglobal\ServiceHandler\Tests\Finder;

use Metglobal\ServiceHandler\Annotation\DI;
use Metglobal\ServiceHandler\Finder\AnnotationFinder;
use Metglobal\ServiceHandler\Finder\DependencyFinder;
use Metglobal\ServiceHandler\Finder\PhpClassFinder;
use PHPUnit\Framework\TestCase;

class DependencyFinderTest extends TestCase
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
     * Some of class has annotations
     *
     * @throws \ReflectionException
     * @group exclude
     */
    public function testExclude()
    {
        /*
            metglobal:
                service_handler:
                    bundles:
                        - 'Gts/ApiBundle'
                    exclude: { 'Gts/ApiBundle': ['Tests'] }
         */
        $phpClassFinderMock = $this->getPhpClassFinderMock(
            'testDir',
            [
                'TestClass', 'Test2Class/Exclude/Me', 'Test2Class/Exclude/Us', 'Test3Class/Exclude/Me', 'Test4Class/Exclude/Me'
            ]
        );
        $annotationFinderMock = $this->getAnnotationFinderMock(
            [
                'TestClass' => $this->getSampleDI('di1')
            ]
        );

        $exclude = [
            'Test2Class/Exclude' => ['Me', 'Us'],
            'Test3Class/Exclude' => ['Me'],
            'Test4Class/Exclude' => 'Me',
        ];

        $dependencyFinder = new DependencyFinder($phpClassFinderMock, $annotationFinderMock);
        $annotations = $dependencyFinder->find('testDir', $exclude);

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
