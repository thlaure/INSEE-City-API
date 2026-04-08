<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\City\Model;

use App\Domain\City\Model\City;
use App\Domain\City\Model\CityCollection;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CityCollectionTest extends TestCase
{
    public function testGetItemsReturnsAllCities(): void
    {
        $city = $this->makeCity('75056', 'Paris');
        $collection = new CityCollection(items: [$city], totalCount: 1);

        $this->assertSame([$city], $collection->getItems());
    }

    public function testGetTotalCountReturnsCount(): void
    {
        $collection = new CityCollection(items: [], totalCount: 42);

        $this->assertSame(42, $collection->getTotalCount());
    }

    public function testIsEmptyReturnsTrueWhenEmpty(): void
    {
        $collection = new CityCollection(items: [], totalCount: 0);

        $this->assertTrue($collection->isEmpty());
    }

    public function testIsEmptyReturnsFalseWhenNotEmpty(): void
    {
        $city = $this->makeCity('75056', 'Paris');
        $collection = new CityCollection(items: [$city], totalCount: 1);

        $this->assertFalse($collection->isEmpty());
    }

    private function makeCity(string $inseeCode, string $name): City
    {
        return new City(
            inseeCode: $inseeCode,
            name: $name,
            departmentCode: '75',
            regionCode: '11',
            postalCode: '',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );
    }
}
