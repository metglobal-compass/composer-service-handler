<?php

namespace Tests\Annotation;

use Metglobal\ServiceHandler\Annotation\Service;
use Metglobal\ServiceHandler\Annotation\Tag;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    public function testYamlConvertion()
    {
        $di = new Service();
        $di->class = "TestBundle\TestService";
        $di->arguments = ['@test_dependency1', '@test_dependency2'];
        $di->factory = ['@test_factory', 'factoryMethod'];
        $di->tags = [
            $this->createTag("name1", "event1", "method1"),
            $this->createTag("name2", "event2", "method2"),
            $this->createTag("name3", "event3", "method3"),
        ];
        $di->calls = ['@test_call', '@test_method'];
        $di->public = true;
        $di->abstract = false;
        $di->parent = '@test_parent_dependency';
        $di->lazy = true;
        $di->autoconfigure = false;
        $di->autowire = true;

        $actual = $di->toYamlArray();

        $expected = [
            "class" => "TestBundle\TestService",
            'arguments' => ['@test_dependency1', '@test_dependency2'],
            'factory' => ['@test_factory', 'factoryMethod'],
            'tags' => [
                ['name' => 'name1', 'event' => 'event1', 'method' => 'method1'],
                ['name' => 'name2', 'event' => 'event2', 'method' => 'method2'],
                ['name' => 'name3', 'event' => 'event3', 'method' => 'method3'],
            ],
            'calls' => ['@test_call', '@test_method'],
            'public' => true,
            'abstract' => false,
            'parent' => '@test_parent_dependency',
            'lazy' => true,
            'autoconfigure' => false,
            'autowire' => true,

        ];

        $this->assertEquals($expected, $actual);
    }

    public function testYamlOptionalCases()
    {
        $di = new Service();
        $di->class = "TestBundle\TestService";
        $di->arguments = ['@test_dependency1', '@test_dependency2'];

        $actual = $di->toYamlArray();

        $expected = [
            "class" => "TestBundle\TestService",
            'arguments' => ['@test_dependency1', '@test_dependency2'],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @param $name
     * @param $event
     * @param $method
     * @return Tag
     */
    protected function createTag($name, $event, $method)
    {
        $tag = new Tag();
        $tag->name = $name;
        $tag->event = $event;
        $tag->method = $method;

        return $tag;
    }
}
