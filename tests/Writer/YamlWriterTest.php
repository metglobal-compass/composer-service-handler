<?php

namespace Metglobal\ServiceHandler\Tests\Writer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Metglobal\ServiceHandler\Writer\YamlWriter;

class YamlWriterTest extends TestCase
{
    public function testSuccess()
    {
        $testYaml = [
            'services' =>
                [
                    'test_bundle.test_service' => [
                        'class' => 'TestBundle/TestClass',
                        'arguments' => [
                            'test'
                        ],
                        'factory' => ['FactoryClass', 'factoryMethod']
                    ]
                ]
        ];
        $yamlWriter = new YamlWriter();
        $yamlWriter->write("tests/Fixtures/Files/yaml_test.yaml", $testYaml);

        // Parse file manually
        $content = file_get_contents("tests/Fixtures/Files/yaml_test.yaml");
        $writtenYaml = Yaml::parse($content);

        $this->assertEquals($testYaml, $writtenYaml);
    }
}
