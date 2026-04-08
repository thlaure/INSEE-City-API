<?php

declare(strict_types=1);

namespace App\UI\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

final readonly class HealthController
{
    public function __construct(private Connection $connection)
    {
    }

    public function __invoke(): JsonResponse
    {
        try {
            $this->connection->executeQuery('SELECT 1');
        } catch (Throwable) {
            return new JsonResponse(['status' => 'error', 'detail' => 'Database unavailable'], \Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return new JsonResponse(['status' => 'ok']);
    }
}
