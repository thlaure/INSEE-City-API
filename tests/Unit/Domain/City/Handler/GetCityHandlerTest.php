<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\City\Handler;

use App\Domain\City\Exception\CityNotFoundException;
use App\Domain\City\Handler\GetCityHandler;
use App\Domain\City\Model\City;
use App\Domain\City\Port\CityRepositoryInterface;
use App\Domain\City\Query\GetCityQuery;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetCityHandlerTest extends TestCase
{
    private CityRepositoryInterface&MockObject $cityRepository;

    private GetCityHandler $handler;

    protected function setUp(): void
    {
        $this->cityRepository = $this->createMock(CityRepositoryInterface::class);
        $this->handler = new GetCityHandler($this->cityRepository);
    }

    public function testInvokeReturnsCityWhenFound(): void
    {
        $city = new City(
            inseeCode: '75056',
            name: 'Paris',
            departmentCode: '75',
            regionCode: '11',
            population: 2133111,
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );

        $this->cityRepository->expects($this->once())
            ->method('findByInseeCode')
            ->with('75056')
            ->willReturn($city);

        $result = ($this->handler)(new GetCityQuery('75056'));

        $this->assertSame($city, $result);
    }

    public function testInvokeThrowsCityNotFoundExceptionWhenNotFound(): void
    {
        $this->cityRepository->expects($this->once())
            ->method('findByInseeCode')
            ->with('99999')
            ->willReturn(null);

        $this->expectException(CityNotFoundException::class);
        $this->expectExceptionMessage('City with INSEE code "99999" was not found.');

        ($this->handler)(new GetCityQuery('99999'));
    }
}
