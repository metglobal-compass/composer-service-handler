<?php

namespace SymfonyAutoDiYml\Tests\Finder;

use SymfonyAutoDiYml\Finder\YamlParser;
use SymfonyAutoDiYml\Tests\BaseTestCase;

class YamlParserTest extends BaseTestCase
{
    public function testSuccess()
    {
        $yamlParser = new YamlParser();
        $item = $yamlParser->parse("tests/Fixtures/Files/valid_yaml.yaml");

        $this->assertEquals(['services' => ["test" => "value"]], $item);
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function testFailure()
    {
        $yamlParser = new YamlParser();
        $item = $yamlParser->parse("tests/Fixtures/Files/invalid_yaml.yaml");

        $this->assertEquals(false, $item);
    }
}
