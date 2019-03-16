<?php

namespace Tests;

use Metglobal\ServiceHandler\Processor;
use PHPUnit\Framework\TestCase;

class ProcessorTest extends TestCase
{
    private $io;

    /**
     * @var Processor
     */
    private $processor;

    protected function setUp()
    {
        parent::setUp();

        $this->io = $this->prophesize('Composer\IO\IOInterface');
        $this->processor = new Processor($this->io->reveal());
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

        $this->processor->processFile($config);
    }

    public function provideInvalidConfiguration()
    {
        return [
            'no config' => [
                [],
                'The extra.metglobal-services.file setting is required to use this script handler.',
            ],
//            'no config parameters' => [
//                [
//                    'parameters' => [],
//                ],
//                'The parameters.service_handler setting is required to use this script handler.',
//            ],
//            'no valid config parameters' => [
//                [
//                    'parameters' => [
//                        'service_handler' => [
//                            'TestBundle\\' => [],
//                        ],
//                    ],
//                ],
//                'The parameters.service_handler.bundles setting is required to use this script handler.',
//            ],
//            'no exclude config parameters' => [
//                [
//                    'parameters' => [
//                        'service_handler' => [
//                            'TestBundle\\' => [],
//                            'exclude' => [],
//                        ],
//                    ],
//                ],
//                'The parameters.service_handler.exclude setting is required to use this script handler.',
//            ],
        ];
    }
}
