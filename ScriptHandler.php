<?php

namespace Metglobal\ServiceHandler;

use Composer\Script\Event;

class ScriptHandler
{
    public static function buildServices(Event $event)
    {
        $extras = $event->getComposer()->getPackage()->getExtra();

        if (!isset($extras['metglobal-services'])) {
            throw new \InvalidArgumentException('The service handler needs to be configured through the extra.metglobal-services setting.');
        }

        $configs = $extras['metglobal-services'];

        if (!is_array($configs)) {
            throw new \InvalidArgumentException('The extra.metglobal-services setting must be an array or a configuration object.');
        }

        if (array_keys($configs) !== range(0, count($configs) - 1)) {
            $configs = [$configs];
        }

        $processor = new Processor($event->getIO());

        foreach ($configs as $config) {
            if (!is_array($config)) {
                throw new \InvalidArgumentException('The extra.metglobal-services setting must be an array of configuration objects.');
            }

            $processor->processFile($config);
        }
    }
}
