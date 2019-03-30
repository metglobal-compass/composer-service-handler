<?php

namespace Metglobal\ServiceHandler\Tests\Finder;

use Metglobal\ServiceHandler\Finder\YamlParser;
use PHPUnit\Framework\TestCase;

class YamlParserTest extends TestCase
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
