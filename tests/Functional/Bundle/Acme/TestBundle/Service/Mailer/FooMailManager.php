<?php

namespace Acme\TestBundle\Service\Mailer;

use Metglobal\ServiceHandler\Annotation\Service;
use Acme\TestBundle\Mailer\MailManager;

/**
 * @Service(
 *     parent="Acme\TestBundle\Mailer\MailManager",
 *     public=true,
 *     autowire=true,
 *     autoconfigure=false
 * )
 */
class FooMailManager extends MailManager
{

}