<?php

namespace Metglobal\ServiceHandler\Finder;

use Symfony\Component\Yaml\Yaml;

class YamlParser
{
    public function parse($path)
    {
        return Yaml::parse(file_get_contents($path));
    }
}
