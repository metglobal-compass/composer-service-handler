<?php

namespace Metglobal\ServiceHandler\tests\Functional;

use Metglobal\ServiceHandler\Processor;
use PHPUnit\Framework\TestCase;

class BundleTest extends TestCase
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
    public function testBundle(array $config)
    {
        chdir(__DIR__);

        $actual = $this->processor->processFile($config);

        $expected = [
            'TestBundle\\' => [
                'services' => [
                    'TestBundle\Mailer\MailManager' => [
                        'arguments' => [
                            '%kernel.debug%',
                        ],
                    ],
                    'TestBundle\Service\BarService' => [
                        'public' => true,
                        'autowire' => true,
                        'autoconfigure' => false,
                    ],
                    'test.baz_service' => [
                        'class' => 'TestBundle\Service\BazService',
                        'public' => true,
                        'autowire' => true,
                        'autoconfigure' => false,
                    ],
                    'TestBundle\Service\Mailer\FooMailManager' => [
                        'public' => true,
                        'parent' => 'TestBundle\Mailer\MailManager',
                        'autowire' => true,
                        'autoconfigure' => false,
                    ],
                    'TestBundle\Service\FooService' => [
                        'public' => true,
                        'autowire' => true,
                        'autoconfigure' => false,
                    ],
                    'TestBundle\EventListener\FooControllerListener' => [
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
            'Acme\\TestBundle\\' => [
                'services' => [
                    'Acme\TestBundle\Mailer\MailManager' => [
                        'arguments' => [
                            '%kernel.debug%',
                        ],
                    ],
                    'Acme\TestBundle\Service\BarService' => [
                        'public' => true,
                        'autowire' => true,
                        'autoconfigure' => false,
                    ],
                    'test.baz_service' => [
                        'class' => 'Acme\TestBundle\Service\BazService',
                        'public' => true,
                        'autowire' => true,
                        'autoconfigure' => false,
                    ],
                    'Acme\TestBundle\Service\Mailer\FooMailManager' => [
                        'public' => true,
                        'parent' => 'Acme\TestBundle\Mailer\MailManager',
                        'autowire' => true,
                        'autoconfigure' => false,
                    ],
                    'Acme\TestBundle\Service\FooService' => [
                        'public' => true,
                        'autowire' => true,
                        'autoconfigure' => false,
                    ],
                    'Acme\TestBundle\EventListener\FooControllerListener' => [
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
                    'file' => '../Functional/app/config/services.yml',
                ],
            ],
        ];
    }
}