<?php

namespace TestBundle\Mailer;

class MailManager
{
    private $debug;

    public function __construct(bool $debug)
    {
        $this->debug = $debug;
    }
}