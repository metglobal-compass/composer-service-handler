<?php

namespace Tests;

use Metglobal\ServiceHandler\ScriptHandler;
use PHPUnit\Framework\TestCase;

class ScriptHandlerTest extends TestCase
{
    private $event;
    private $io;
    private $package;

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
    public function testInvalidConfiguration(array $config, $exceptionMessage)
    {
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
            'no composer config' => [
                [],
                'The service handler needs to be configured through the extra.metglobal-services setting.',
            ],
//            'no valid composer config' => [
//                [
//                    'metglobal-services' => '',
//                ],
//                'The extra.metglobal-services setting must be an array or a configuration object.',
//            ],
//            'no valid composer config as array' => [
//                [
//                    'metglobal-services' => [
//                        '',
//                    ],
//                ],
//                'The extra.metglobal-services setting must be an array of configuration objects.',
//            ],
        ];
    }
}