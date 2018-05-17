<?php

namespace SymfonyAutoDiYml\Tests\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use SymfonyAutoDiYml\Finder\PhpClassFinder;
use SymfonyAutoDiYml\Tests\BaseTestCase;

class PhpClassFinderTest extends BaseTestCase
{
    public function testSuccess()
    {
        $fileMocks = [
            $this->getSplFileInfoMock(
                "TestClass",
                "
                            <?php 
                            namespace Base\Part;
                            
                            class TestClass {
                            
                            }
                         "
            ),
            $this->getSplFileInfoMock(
                "AnotherTestClass",
                "
                    <?php
                    
                    class AnotherTestClass {
                    
                    }
                        "
            )
        ];

        $finderMock = $this->getFinderMock($fileMocks);

        $phpClassFinder = new PhpClassFinder($finderMock);

        $classes = $phpClassFinder->find("testDir");

        $this->assertEquals(["Base\Part\TestClass", "\AnotherTestClass"], $classes);
    }

    public function testEmptyDir()
    {
        $finderMock = $this->getFinderMock([]);
        $phpClassFinder = new PhpClassFinder($finderMock);

        $classes = $phpClassFinder->find("testDir");

        $this->assertEquals([], $classes);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFinderMock($files)
    {
        $finderMock = $this
            ->getMockBuilder(Finder::class)
            ->getMock();

        $finderMock
            ->expects($this->exactly(1))
            ->method("in")
            ->with("testDir")
            ->willReturnSelf();

        $finderMock
            ->expects($this->exactly(1))
            ->method("files")
            ->willReturnSelf();

        $finderMock
            ->expects($this->exactly(1))
            ->method("name")
            ->with("*.php")
            ->willReturnSelf();

        $finderMock
            ->expects($this->exactly(1))
            ->method("getIterator")
            ->willReturn($files);

        return $finderMock;
    }

    /**
     * @param $fileName
     * @param $contents
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getSplFileInfoMock($fileName, $contents)
    {
        $splFileInfoMock = $this
            ->getMockBuilder(SplFileInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $splFileInfoMock
            ->expects($this->exactly(1))
            ->method("getFileName")
            ->willReturn($fileName);

        $splFileInfoMock
            ->expects($this->exactly(1))
            ->method("getContents")
            ->willReturn($contents);

        return $splFileInfoMock;
    }
}
