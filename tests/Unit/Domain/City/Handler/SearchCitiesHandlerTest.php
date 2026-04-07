<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\City\Handler;

use App\Domain\City\Handler\SearchCitiesHandler;
use App\Domain\City\Model\CityCollection;
use App\Domain\City\Model\CitySearchCriteria;
use App\Domain\City\Port\CityRepositoryInterface;
use App\Domain\City\Query\SearchCitiesQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SearchCitiesHandlerTest extends TestCase
{
    private CityRepositoryInterface&MockObject $cityRepository;

    private SearchCitiesHandler $handler;

    protected function setUp(): void
    {
        $this->cityRepository = $this->createMock(CityRepositoryInterface::class);
        $this->handler = new SearchCitiesHandler($this->cityRepository);
    }

    public function testInvokeWithNoFiltersReturnsFullCollection(): void
    {
        $expected = new CityCollection(items: [], totalCount: 0);

        $this->cityRepository->expects($this->once())
            ->method('findByCriteria')
            ->with($this->callback(static fn (CitySearchCriteria $c): bool => $c->name === null
                && $c->departmentCode === null
                && $c->regionCode === null
                && $c->page === 1
                && $c->itemsPerPage === 30))
            ->willReturn($expected);

        $result = ($this->handler)(new SearchCitiesQuery());

        $this->assertSame($expected, $result);
    }

    public function testInvokeWithFiltersPassesCriteriaToRepository(): void
    {
        $expected = new CityCollection(items: [], totalCount: 0);

        $this->cityRepository->expects($this->once())
            ->method('findByCriteria')
            ->with($this->callback(static fn (CitySearchCriteria $c): bool => $c->name === 'paris'
                && $c->departmentCode === '75'
                && $c->regionCode === '11'
                && $c->page === 2
                && $c->itemsPerPage === 10))
            ->willReturn($expected);

        $result = ($this->handler)(new SearchCitiesQuery(
            name: 'paris',
            departmentCode: '75',
            regionCode: '11',
            page: 2,
            itemsPerPage: 10,
        ));

        $this->assertSame($expected, $result);
    }
}
