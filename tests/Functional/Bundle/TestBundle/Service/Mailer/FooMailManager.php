<?php

namespace TestBundle\Service\Mailer;

use Metglobal\ServiceHandler\Annotation\Service;
use TestBundle\Mailer\MailManager;

/**
 * @Service(
 *     parent="TestBundle\Mailer\MailManager",
 *     public=true,
 *     autowire=true,
 *     autoconfigure=false
 * )
 */
class FooMailManager extends MailManager
{

}