<?php

namespace Metglobal\ServiceHandler\Tests\Annotation;

use Metglobal\ServiceHandler\Annotation\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    public function testSuccess()
    {
        $tag = new Tag();
        $tag->name = "test_tag_name";
        $tag->event = "test_tag_event";
        $tag->method = "test_tag_method";
        $tag->priority = 5;

        $actual = $tag->toYamlArray();

        $expected = [
            'name' => 'test_tag_name',
            'event' => 'test_tag_event',
            'method' => 'test_tag_method',
            'priority' => 5,
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testOptionalCases()
    {
        $tag = new Tag();
        $tag->name = "test_tag_name";

        $actual = $tag->toYamlArray();

        $expected = [
            'name' => 'test_tag_name',
        ];

        $this->assertEquals($expected, $actual);
    }
}
