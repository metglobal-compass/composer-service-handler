<?php

namespace Metglobal\ServiceHandler\Annotation;

interface YamlConvertable
{
    /** @return array */
    public function toYamlArray();
}
