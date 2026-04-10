<?php

declare(strict_types=1);

namespace App\Tests\Unit\UI\Controller;

use App\UI\Controller\HealthController;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class HealthControllerTest extends TestCase
{
    public function testInvokeReturnsOkWhenDatabaseIsReachable(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection
            ->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT 1');

        $controller = new HealthController($connection);

        $response = $controller->__invoke();

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('{"status":"ok"}', $response->getContent());
    }

    public function testInvokeReturnsServiceUnavailableWhenDatabaseFails(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection
            ->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT 1')
            ->willThrowException(new RuntimeException('db down'));

        $controller = new HealthController($connection);

        $response = $controller->__invoke();

        self::assertSame(Response::HTTP_SERVICE_UNAVAILABLE, $response->getStatusCode());
        self::assertSame('{"status":"error","detail":"Database unavailable"}', $response->getContent());
    }
}
