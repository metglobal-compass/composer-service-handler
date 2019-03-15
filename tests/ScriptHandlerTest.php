<?php

namespace Metglobal\ServiceHandler\Tests;

use Metglobal\ServiceHandler\Finder\ConfigFinder;
use Metglobal\ServiceHandler\ScriptHandler;
use PHPUnit\Framework\TestCase;

class ScriptHandlerTest extends TestCase
{
    private $event;
    private $io;
    private $package;
    private $configFinder;

    protected function setUp()
    {
        parent::setUp();

        $this->event = $this->prophesize('Composer\Script\Event');
        $this->io = $this->prophesize('Composer\IO\IOInterface');
        $this->package = $this->prophesize('Composer\Package\PackageInterface');
        $composer = $this->prophesize('Composer\Composer');

        $composer->getPackage()->willReturn($this->package);
        $this->event->getComposer()->willReturn($composer);
        $this->event->getIO()->willReturn($this->io);
    }

    /**
     * @dataProvider provideInvalidConfiguration
     */
    public function InvalidConfiguration(array $config, $exceptionMessage)
    {
        $this->configFinder = $this->getConfigFinderMock(
            $config['parameters']['service_handler']['bundles'],
            $config['parameters']['service_handler']['exclude']
        );

        if (method_exists($this, 'expectException')) {
            $this->expectException('InvalidArgumentException');
            $this->expectExceptionMessage($exceptionMessage);
        } else {
            $this->setExpectedException('InvalidArgumentException', $exceptionMessage);
        }

        ScriptHandler::buildServices($this->event->reveal());
    }

    public function provideInvalidConfiguration()
    {
        return [
            'no bundles config' => [
                [
                    'parameters' => [
                        'service_handler' => [
                            'bundles' => [],
                            'exclude' => [],
                        ],
                    ],
                ],
                'The parameters.service_handler.bundles setting is required to use this script handler.',
            ],
            'no exclude config' => [
                [
                    'parameters' => [
                        'service_handler' => [
                            'bundles' => ['TestBundle'],
                            'exclude' => [],
                        ],
                    ],
                ],
                'The parameters.service_handler.exclude setting is required to use this script handler.',
            ],
        ];
    }

    private function getConfigFinderMock($bundles, $exclude)
    {
        $configFinderMock = $this
            ->getMockBuilder(ConfigFinder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configFinderMock
            ->expects($this->exactly(1))
            ->method("getConfigYml")
            ->willReturn(['parameters' => ['service_handler' => ['bundles' => $bundles, 'exclude' => $exclude]]]);

        return $configFinderMock;
    }
}