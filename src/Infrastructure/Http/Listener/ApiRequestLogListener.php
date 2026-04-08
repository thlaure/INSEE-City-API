<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Listener;

use function bin2hex;
use function hrtime;
use function preg_replace;

use Psr\Log\LoggerInterface;

use function random_bytes;
use function round;
use function str_starts_with;
use function substr;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, method: 'onRequest', priority: 10)]
#[AsEventListener(event: KernelEvents::RESPONSE, method: 'onResponse')]
final class ApiRequestLogListener
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.api_access')]
        private readonly LoggerInterface $logger,
    ) {
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $requestId = $request->headers->get('X-Request-Id') ?? bin2hex(random_bytes(8));
        $request->attributes->set('_request_id', $requestId);
        $request->attributes->set('_api_log_start', hrtime(true));
    }

    public function onResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        $response = $event->getResponse();
        $requestId = $request->attributes->get('_request_id', '');
        $response->headers->set('X-Request-Id', $requestId);

        $startTime = $request->attributes->get('_api_log_start');
        $durationMs = $startTime !== null
            ? (int) round((hrtime(true) - $startTime) / 1_000_000)
            : null;

        $rawConsumer = $request->headers->get('X-App-Name', 'anonymous');
        $consumer = preg_replace('/[^a-zA-Z0-9_-]/', '', substr($rawConsumer, 0, 64)) ?: 'anonymous';

        $this->logger->info('api.request', [
            'request_id' => $requestId,
            'consumer' => $consumer,
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
            'status' => $response->getStatusCode(),
            'ip' => $request->getClientIp(),
            'user_agent' => $request->headers->get('User-Agent'),
            'duration_ms' => $durationMs,
        ]);
    }
}
