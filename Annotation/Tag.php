<?php

namespace Metglobal\ServiceHandler\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @package Metglobal\ServiceHandler\Annotation
 */
class Tag implements YamlConvertible
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
     * @var int
     */
    public $priority;

    /**
     * @inheritDoc
     */
    public function toYamlArray(): array
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

        if ($this->priority != null) {
            $item['priority'] = $this->priority;
        }

        return $item;
    }
}
