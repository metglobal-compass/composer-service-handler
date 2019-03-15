<?php

namespace Metglobal\ServiceHandler\Tests\Finder;

use Composer\Composer;
use Composer\Package\RootPackage;
use Composer\Script\Event;
use Metglobal\ServiceHandler\Finder\ComposerParser;
use PHPUnit\Framework\TestCase;

class ComposerParserTest extends TestCase
{
    /**
     * Success test
     */
    public function testSuccess()
    {
        $eventMock = $this->getEventMockWithExtras(['symfony-app-dir' => 'bin']);

        $configFinder = new ComposerParser($eventMock);
        $dir = $configFinder->getSymfonyAppDir();

        $this->assertEquals('bin', $dir);
    }

    /**
     * If symfony-app-dir value is not set it should be default option
     */
    public function testDefaultOption()
    {
        $eventMock = $this->getEventMockWithExtras([]);

        $configFinder = new ComposerParser($eventMock);
        $dir = $configFinder->getSymfonyAppDir();

        $this->assertEquals('app', $dir);
    }

    /**
     * @param $extras
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventMockWithExtras($extras)
    {
        $package = new RootPackage("test", "1.0", "v1.0");
        $package->setExtra($extras);

        $composer = new Composer();
        $composer->setPackage($package);

        $eventMock = $this
            ->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock
            ->expects($this->exactly(1))
            ->method('getComposer')
            ->willReturn($composer);

        return $eventMock;
    }
}
