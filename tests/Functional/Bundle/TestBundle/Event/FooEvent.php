<?php

namespace TestBundle\Event;

use Metglobal\ServiceHandler\Annotation\Service;

/**
 * This file excluded in service.yml so service not build.
 *
 * @Service(
 *     public=true,
 *     autowire=true,
 *     autoconfigure=false
 * )
 */
class FooEvent
{

}