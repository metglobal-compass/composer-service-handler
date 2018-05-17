<?php

namespace SymfonyAutoDiYml\Tests\Writer;

use Symfony\Component\Yaml\Yaml;
use SymfonyAutoDiYml\Tests\BaseTestCase;
use SymfonyAutoDiYml\Writer\YamlWriter;

class YamlWriterTest extends BaseTestCase
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
