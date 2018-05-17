<?php

namespace SymfonyAutoDiYml\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @package SymfonyAutoDiYml\Annotation
 */
class Tag implements YamlConvertable
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $event;

    /**
     * @var string
     */
    public $method;

    /**
     * @inheritDoc
     */
    public function toYamlArray()
    {
        $item = [
            'name' => $this->name,
        ];

        if ($this->event != null) {
            $item['event'] = $this->event;
        }

        if ($this->method != null) {
            $item['method'] = $this->method;
        }

        return $item;
    }
}
