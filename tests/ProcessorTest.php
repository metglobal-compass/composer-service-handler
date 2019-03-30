<?php

namespace Metglobal\ServiceHandler\Tests;

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
                [
                    'parameters' => [],
                ],
                'The parameters.service_handler setting is required to use this script handler.',
            ],
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
}
