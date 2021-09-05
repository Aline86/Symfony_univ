<?php
namespace App\EventSuscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class Creation implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'prePersist',
        );
    }
}