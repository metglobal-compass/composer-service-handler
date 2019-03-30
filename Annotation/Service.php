<?php

namespace Metglobal\ServiceHandler\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Service implements YamlConvertible
{
    /**
     * @Required
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $class;

    /**
     * @var array
     */
    public $arguments;

    /**
     * @var array
     */
    public $factory;

    /**
     * @var array<Metglobal\ServiceHandler\Annotation\Tag>
     */
    public $tags;

    /**
     * @var array
     */
    public $calls;

    /**
     * @var bool
     */
    public $public;

    /**
     * @var bool
     */
    public $abstract;

    /**
     * @var string
     */
    public $parent;

    /**
     * @var bool
     */
    public $lazy;

    /**
     * @var bool
     */
    public $autowire;

    /**
     * @var bool
     */
    public $autoconfigure;

    /**
     * @inheritDoc
     */
    public function toYamlArray(): array
    {
        $item = [
            'class' => $this->class,
        ];

        $optionalFields = [
            'arguments',
            'factory',
            'calls',
            'public',
            'abstract',
            'parent',
            'lazy',
            'autowire',
            'autoconfigure',
        ];

        foreach ($optionalFields as $field) {
            if ($this->$field !== null) {
                $item[$field] = $this->$field;
            }
        }

        if ($this->tags != null) {
            $item['tags'] = array_map(
                function (Tag $tag) {
                    return $tag->toYamlArray();
                },
                $this->tags
            );
        }

        return $item;
    }
}
