<?php

namespace Metglobal\ServiceHandler;

use Composer\Script\Event;
use Metglobal\ServiceHandler\Finder\ComposerParser;
use Metglobal\ServiceHandler\Finder\ConfigFinder;
use Metglobal\ServiceHandler\Finder\YamlParser;

class ScriptHandler
{
    public static function buildServices(Event $event)
    {
        $configFinder = new ConfigFinder(new ComposerParser($event), new YamlParser());

        $config = $configFinder->getConfigYml();

        $processor = new Processor($event->getIO());

        $processor->processFile($config);
    }
}
