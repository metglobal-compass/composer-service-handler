<?php

namespace Metglobal\ServiceHandler\tests\Functional;

use Metglobal\ServiceHandler\Processor;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
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
     * @dataProvider provideConfiguration
     */
    public function testApp(array $config)
    {
        chdir(__DIR__);

        $actual = $this->processor->processFile($config);

        $expected = [
            'App\\' => [
                'services' => [
                    'App\Service\BarService' => [
                        'public' => true,
                        'autowire' => true,
                        'autoconfigure' => false,
                    ],
                    'test.baz_service' => [
                        'class' => 'App\Service\BazService',
                        'public' => true,
                        'autowire' => true,
                        'autoconfigure' => false,
                    ],
                    'App\Service\Mailer\FooMailManager' => [
                        'public' => true,
                        'parent' => 'App\Mailer\MailManager',
                        'autowire' => true,
                        'autoconfigure' => false,
                    ],
                    'App\Service\FooService' => [
                        'public' => true,
                        'autowire' => true,
                        'autoconfigure' => false,
                    ],
                    'App\EventListener\FooControllerListener' => [
                        'arguments' => [
                            '@service_container',
                        ],
                        'tags' => [
                            [
                                'name' => 'kernel.event_listener',
                                'event' => 'kernel.controller',
                                'method' => 'onKernelController',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertSame($expected, $actual);
    }

    public function provideConfiguration()
    {
        return [
            'config' => [
                [
                    'file' => '../Functional/config/services.yaml',
                ],
            ],
        ];
    }
}