<?php

namespace Metglobal\ServiceHandler\Tests;

use Metglobal\ServiceHandler\Annotation\DI;
use Metglobal\ServiceHandler\ScriptHandler;
use Metglobal\ServiceHandler\Finder\ConfigFinder;
use Metglobal\ServiceHandler\Finder\DependencyFinder;
use Metglobal\ServiceHandler\Finder\YamlParser;
use Metglobal\ServiceHandler\Writer\YamlWriter;

class BuilderTest extends BaseTestCase
{
    public function testSuccess()
    {
        $bundles = [
            'TestBundle1' => [
                'dependencies' => [
                    'test_bundle1.di1' => $this->getSampleDI("test_bundle1.di1", 'TestBundle1\Di1'),
                    'test_bundle1.di2' => $this->getSampleDI("test_bundle1.di2", 'TestBundle1\Di2'),
                ],
                'dir' => 'src/TestBundle1',
                'yamlPath' => 'src/TestBundle1/Resources/config/services.yml',
                'distPath' => 'src/TestBundle1/Resources/config/services.yml.dist',
                'distYaml' => [
                    'services' => [
                        'test_bundle_dist1.di1' => [
                            'class' => 'TestBundle1\Dist1'
                        ]
                    ]
                ],
                'yaml' => [
                    'test_bundle1.di1' => [
                        'class' => 'TestBundle1\Di1'
                    ],
                    'test_bundle1.di2' => [
                        'class' => 'TestBundle1\Di2'
                    ]
                ]
            ],
            'TestBundle2' => [
                'dependencies' => [
                    'test_bundle2.di1' => $this->getSampleDI("test_bundle2.di1", 'TestBundle2\Di1'),
                    'test_bundle2.di2' => $this->getSampleDI("test_bundle2.di2", 'TestBundle2\Di2'),
                ],
                'dir' => 'src/TestBundle2',
                'yamlPath' => 'src/TestBundle2/Resources/config/services.yml',
                'distPath' => 'src/TestBundle2/Resources/config/services.yml.dist',
                'distYaml' => [
                    'services' => [
                        'test_bundle_dist2.di1' => [
                            'class' => 'TestBundle2\Dist2'
                        ]
                    ]
                ],
                'yaml' => [
                    'test_bundle2.di1' => [
                        'class' => 'TestBundle2\Di1',
                    ],
                    'test_bundle2.di2' => [
                        'class' => 'TestBundle2\Di2',
                    ]
                ]
            ]
        ];
        $exclude = 'TestBundle1/Tests';

        $configFinderMock = $this->getConfigFinderMock(array_keys($bundles), $exclude);
        $dependencyFinderMock = $this->getDependencyFinderMock($bundles);
        $yamlWriterMock = $this->getYamlWriterMock($bundles);
        $yamlParserMock = $this->getYamlParserMock($bundles);

        $builder = new ScriptHandler($configFinderMock, $dependencyFinderMock, $yamlWriterMock, $yamlParserMock);
        $builder->build();
    }

    /**
     * @param $bundles
     * @param $exclude
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getConfigFinderMock($bundles, $exclude)
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

    /**
     * @param $bundles
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDependencyFinderMock($bundles)
    {
        $dependencyFinderMock = $this
            ->getMockBuilder(DependencyFinder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $i = 0;
        foreach ($bundles as $bundle) {
            $dependencyFinderMock
                ->expects($this->at($i))
                ->method('find')
                ->with($bundle['dir'])
                ->willReturn($bundle['dependencies']);

            $i++;
        }

        return $dependencyFinderMock;
    }

    /**
     * @param $bundles
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getYamlWriterMock($bundles)
    {
        $yamlWriterMock = $this
            ->getMockBuilder(YamlWriter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $i = 0;
        foreach ($bundles as $bundle) {
            $bundle['distYaml']['services'] = array_merge($bundle['distYaml']['services'], $bundle['yaml']);
            $yamlWriterMock
                ->expects($this->at($i))
                ->method('write')
                ->with($bundle['yamlPath'], $bundle['distYaml']);

            $i++;
        }

        return $yamlWriterMock;
    }

    /**
     * @param $id
     * @param $class
     * @return DI
     */
    protected function getSampleDI($id, $class)
    {
        $di = new DI();
        $di->id = $id;
        $di->class = $class;

        return $di;
    }

    /**
     * @param $bundles
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getYamlParserMock($bundles)
    {
        $yamlParserMock = $this
            ->getMockBuilder(YamlParser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $i = 0;
        foreach ($bundles as $bundle) {
            $yamlParserMock
                ->expects($this->at($i))
                ->method('parse')
                ->with($bundle['distPath'])
                ->willReturn($bundle['distYaml']);

            $i++;
        }

        return $yamlParserMock;
    }
}
