<?php

namespace SymfonyAutoDiYml\Annotation;

interface YamlConvertable
{
    /** @return array */
    public function toYamlArray();
}
