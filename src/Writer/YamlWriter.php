<?php

namespace Metglobal\ServiceHandler\Writer;

use Symfony\Component\Yaml\Yaml;

class YamlWriter
{
    /**
     * @param $path
     * @param $array
     */
    public function write($path, $array)
    {
        $data = Yaml::dump($array, 4, 4, true);

        file_put_contents($path, $data);
    }
}
