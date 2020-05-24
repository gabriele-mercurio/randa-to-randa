<?php

namespace App\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CORS implements EventSubscriberInterface {
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['handleCORSRequest', 9999]],
            KernelEvents::RESPONSE => [['handleCORSResponse', 9999]],
        ];
    }

    public function handleCORSRequest(RequestEvent $event) {
        // Don't do anything if it's not the master request.
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        $method  = $request->getRealMethod();
        if ('OPTIONS' == $method) {
            $response = new Response();
            $event->setResponse($response);
        }
    }

    public function handleCORSResponse(ResponseEvent $event) {
        // Don't do anything if it's not the master request.
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Headers', 'DNT, X-User-Token, Keep-Alive, User-Agent, X-Requested-With, If-Modified-Since, Cache-Control, Content-Type, Accept, Origin, Authorization');
        $response->headers->set('Access-Control-Allow-Credentials', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');
        $response->headers->set('Access-Control-Expose-Headers', 'X-Pagination-TotalCount, X-Pagination-PageSize, X-Pagination-PageCount, X-Pagination-PageNumber');
    }
}