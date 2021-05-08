<?php

namespace App\EventListener;

use Metglobal\ServiceHandler\Annotation\Service;
use Metglobal\ServiceHandler\Annotation\Tag;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @Service(
 *     tags = {
 *          @Tag(name="kernel.event_listener", event="kernel.controller", method="onKernelController")
 *     },
 *     arguments={
 *          "@service_container",
 *     }
 * )
 */
class FooControllerListener
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelController()
    {

    }
}
