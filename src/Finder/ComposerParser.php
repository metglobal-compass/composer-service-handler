<?php

namespace SymfonyAutoDiYml\Finder;

use Composer\Script\Event;

class ComposerParser
{
    /**
     * @var Event
     */
    private $event;

    /**
     * ComposerParser constructor.
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getSymfonyAppDir()
    {
        $extras = $this->event->getComposer()->getPackage()->getExtra();

        $symfonyAppDir = isset($extras['symfony-app-dir']) ? $extras['symfony-app-dir'] : "app";

        return $symfonyAppDir;
    }
}
