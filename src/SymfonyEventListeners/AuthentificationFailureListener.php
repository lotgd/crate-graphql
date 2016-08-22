<?php

namespace LotGD\Crate\GraphQL\SymfonyEventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

use LotGD\Crate\GraphQL\Exceptions\AuthenticationException;

class AuthentificationFailureListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return ["kernel.exception" => ['catchException', 200]];
    }

    /**
     * @param GetResponseForExceptionEvent $evt
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        
        if ($exception instanceof AuthenticationException) {
            // If the exception is an authentication exception, we do our stuff.
            $response = new \Symfony\Component\HttpFoundation\JsonResponse([
                "error" => [$exception->getMessage()]
            ], 401);

            $event->stopPropagation();
            $event->setResponse($response);
        }
    }
}