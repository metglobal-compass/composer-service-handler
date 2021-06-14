<?php

namespace App\Service\Mailer;

use Metglobal\ServiceHandler\Annotation\Service;
use App\Mailer\MailManager;

/**
 * @Service(
 *     parent="App\Mailer\MailManager",
 *     public=true,
 *     autowire=true,
 *     autoconfigure=false
 * )
 */
class FooMailManager extends MailManager
{

}
