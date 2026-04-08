<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Listener;

use function str_starts_with;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

#[AsEventListener(event: KernelEvents::REQUEST, method: 'onRequest', priority: 20)]
final class RateLimitListener
{
    public function __construct(private readonly RateLimiterFactory $apiLimiter)
    {
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        $limiter = $this->apiLimiter->create($request->getClientIp() ?? 'unknown');
        $limit = $limiter->consume();

        if (!$limit->isAccepted()) {
            $event->setResponse(new JsonResponse(
                [
                    'type' => 'https://tools.ietf.org/html/rfc6585#section-4',
                    'title' => 'Too Many Requests',
                    'status' => Response::HTTP_TOO_MANY_REQUESTS,
                    'detail' => 'Rate limit exceeded. Try again later.',
                ],
                Response::HTTP_TOO_MANY_REQUESTS,
                ['Content-Type' => 'application/problem+json'],
            ));
        }
    }
}
