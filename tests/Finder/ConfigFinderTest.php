<?php

namespace Metglobal\ServiceHandler\Tests\Finder;

use Metglobal\ServiceHandler\Finder\ComposerParser;
use Metglobal\ServiceHandler\Finder\ConfigFinder;
use Metglobal\ServiceHandler\Finder\YamlParser;
use Metglobal\ServiceHandler\Tests\BaseTestCase;

class ConfigFinderTest extends BaseTestCase
{
    public function testSuccessfulParsing()
    {
        $composerParserMock = $this->getComposerParserMock();

        $parsedYaml = [
            "parameters" => [
                "service_handler" => [
                    "bundles" => ["Gts/ApiBundle"],
                    "exclude" => []
                ]
            ]
        ];

        $yamlParserMock = $this->getYamlParserMock($parsedYaml);

        $configFinder = new ConfigFinder($composerParserMock, $yamlParserMock);
        $realYaml = $configFinder->getConfigYml();

        $this->assertEquals($parsedYaml, $realYaml);
    }

    /**
     * ConfigFinder must return empty list if parameter does not exists
     */
    public function testNonExistingParameter()
    {
        $composerParserMock = $this->getComposerParserMock();

        $yamlParserMock = $this->getYamlParserMock(["parameters" => []]);

        $configFinder = new ConfigFinder($composerParserMock, $yamlParserMock);
        $realYaml = $configFinder->getConfigYml();

        $this->assertEquals(
            [
                'parameters' => [
                    'service_handler' => [
                        "exclude" => []
                    ]
                ]
            ],
            $realYaml
        );
    }

    /**
     * ConfigFinder must throw exception if parameter is not an array
     *
     * @expectedException \InvalidArgumentException
     */
    public function testNotValidParameter()
    {
        $parsedYaml = [
            'parameters' => [
                'service_handler' => [
                    'bundles' => "wrongval",
                ]
            ]
        ];

        $composerParserMock = $this->getComposerParserMock();
        $yamlParserMock = $this->getYamlParserMock($parsedYaml);

        $configFinder = new ConfigFinder($composerParserMock, $yamlParserMock);
        $configFinder->getConfigYml();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getComposerParserMock()
    {
        $mock = $composerParserMock = $this
            ->getMockBuilder(ComposerParser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->expects($this->exactly(1))
            ->method("getSymfonyAppDir")
            ->willReturn("app");

        return $mock;
    }

    /**
     * @param $returningYaml
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getYamlParserMock($returningYaml)
    {
        $yamlParserMock = $this
            ->getMockBuilder(YamlParser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $yamlParserMock
            ->expects($this->exactly(1))
            ->method("parse")
            ->willReturn($returningYaml);

        return $yamlParserMock;
    }
}
