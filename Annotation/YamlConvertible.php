<?php

namespace Metglobal\ServiceHandler\Annotation;

interface YamlConvertible
{
    public function toYamlArray(): array;
}
