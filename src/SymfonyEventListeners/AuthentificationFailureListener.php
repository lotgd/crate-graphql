<?php

namespace LotGD\Crate\GraphQL\SymfonyEventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthentificationFailureListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return ["kernel.exception" => ['catchException', 200]];
    }

    /**
     * @param GetResponseForExceptionEvent $evt
     */
    public function catchException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        
        if ($exception instanceof AuthenticationException) {
            $response = new \Symfony\Component\HttpFoundation\JsonResponse([
                "error" => [$exception->getMessage()]
            ], 401);
            
            $event->stopPropagation();
            $event->setResponse($response);
        }
    }
}