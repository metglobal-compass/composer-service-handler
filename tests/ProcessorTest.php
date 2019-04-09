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
        chdir(__DIR__);

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
            'no config parameters' => [
                [
                    'file' => 'Fixtures/Files/invalid_existing_service_handler.yaml',
                ],
                'The parameters.service_handler setting is required to use this script handler.',
            ],
            'no valid config parameters' => [
                [
                    'file' => 'Fixtures/Files/invalid_existing_bundle.yaml',
                ],
                'The parameters.service_handler.resource setting is required to use this script handler.',
            ],
            'no exclude config parameters' => [
                [
                    'file' => 'Fixtures/Files/invalid_existing_bundle_values.yaml',
                ],
                'The parameters.service_handler.exclude setting is required to use this script handler.',
            ],
        ];
    }
}
