<?php

namespace Metglobal\ServiceHandler;

class Reflector
{
    /**
     * @param $className
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    public function getReflectionClass($className)
    {
        return new \ReflectionClass($className);
    }
}
