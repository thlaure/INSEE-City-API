<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\City\Handler;

use App\Domain\City\Exception\CityDataProviderException;
use App\Domain\City\Handler\ImportCitiesHandler;
use App\Domain\City\Port\CityDataProviderInterface;
use App\Domain\City\Port\CityRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ImportCitiesHandlerTest extends TestCase
{
    private CityDataProviderInterface&MockObject $dataProvider;

    private CityRepositoryInterface&MockObject $cityRepository;

    private ImportCitiesHandler $handler;

    protected function setUp(): void
    {
        $this->dataProvider = $this->createMock(CityDataProviderInterface::class);
        $this->cityRepository = $this->createMock(CityRepositoryInterface::class);
        $this->handler = new ImportCitiesHandler($this->dataProvider, $this->cityRepository);
    }

    public function testInvokeWithEmptyDataReturnsZeroTotals(): void
    {
        $this->dataProvider->expects($this->once())
            ->method('fetchAllCommunes')
            ->willReturn([]);

        $this->cityRepository->expects($this->once())
            ->method('flush');

        $result = ($this->handler)();

        $this->assertSame(0, $result->created);
        $this->assertSame(0, $result->updated);
        $this->assertSame(0, $result->totalProcessed);
    }

    public function testInvokeCreatesNewCitiesAndReturnsCounts(): void
    {
        $raw = [
            ['code' => '75056', 'nom' => 'Paris', 'codeDepartement' => '75', 'codeRegion' => '11', 'population' => 2133111],
            ['code' => '69123', 'nom' => 'Lyon', 'codeDepartement' => '69', 'codeRegion' => '84', 'population' => 522969],
        ];

        $this->dataProvider->expects($this->once())
            ->method('fetchAllCommunes')
            ->willReturn($raw);

        $this->cityRepository->expects($this->exactly(2))
            ->method('save')
            ->willReturn(true);

        $result = ($this->handler)();

        $this->assertSame(2, $result->created);
        $this->assertSame(0, $result->updated);
        $this->assertSame(2, $result->totalProcessed);
    }

    public function testInvokeUpdatesExistingCitiesAndReturnsCounts(): void
    {
        $raw = [
            ['code' => '75056', 'nom' => 'Paris', 'codeDepartement' => '75', 'codeRegion' => '11', 'population' => 2133111],
        ];

        $this->dataProvider->expects($this->once())
            ->method('fetchAllCommunes')
            ->willReturn($raw);

        $this->cityRepository->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $result = ($this->handler)();

        $this->assertSame(0, $result->created);
        $this->assertSame(1, $result->updated);
        $this->assertSame(1, $result->totalProcessed);
    }

    public function testInvokeFlushesEvery50Cities(): void
    {
        $raw = array_fill(0, 100, ['code' => '75056', 'nom' => 'Paris', 'codeDepartement' => '75', 'codeRegion' => '11', 'population' => null]);

        $this->dataProvider->expects($this->once())
            ->method('fetchAllCommunes')
            ->willReturn($raw);

        $this->cityRepository->expects($this->any())
            ->method('save')
            ->willReturn(true);

        // flush called at 50, 100, and final = 3 times
        $this->cityRepository->expects($this->exactly(3))
            ->method('flush');

        ($this->handler)();
    }

    public function testInvokeThrowsWhenDataProviderFails(): void
    {
        $this->dataProvider->expects($this->once())
            ->method('fetchAllCommunes')
            ->willThrowException(CityDataProviderException::fromPrevious(new RuntimeException('timeout')));

        $this->expectException(CityDataProviderException::class);

        ($this->handler)();
    }
}
