<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\City\Model;

use App\Domain\City\Model\CitySearchCriteria;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CitySearchCriteriaTest extends TestCase
{
    public function testConstructWithDefaultsIsValid(): void
    {
        $criteria = new CitySearchCriteria();

        $this->assertNull($criteria->name);
        $this->assertNull($criteria->departmentCode);
        $this->assertNull($criteria->regionCode);
        $this->assertSame(1, $criteria->page);
        $this->assertSame(30, $criteria->itemsPerPage);
    }

    public function testConstructWithAllFilters(): void
    {
        $criteria = new CitySearchCriteria(
            name: 'paris',
            departmentCode: '75',
            regionCode: '11',
            page: 3,
            itemsPerPage: 50,
        );

        $this->assertSame('paris', $criteria->name);
        $this->assertSame('75', $criteria->departmentCode);
        $this->assertSame('11', $criteria->regionCode);
        $this->assertSame(3, $criteria->page);
        $this->assertSame(50, $criteria->itemsPerPage);
    }

    public function testConstructWithPageZeroThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Page must be >= 1.');

        new CitySearchCriteria(page: 0);
    }

    public function testConstructWithNegativePageThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CitySearchCriteria(page: -1);
    }

    public function testConstructWithItemsPerPageZeroThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Items per page must be between 1 and 100.');

        new CitySearchCriteria(itemsPerPage: 0);
    }

    public function testConstructWithItemsPerPageOver100ThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CitySearchCriteria(itemsPerPage: 101);
    }

    public function testConstructWithBoundaryValuesIsValid(): void
    {
        $minPage = new CitySearchCriteria(page: 1, itemsPerPage: 1);
        $maxPage = new CitySearchCriteria(page: 999, itemsPerPage: 100);

        $this->assertSame(1, $minPage->page);
        $this->assertSame(1, $minPage->itemsPerPage);
        $this->assertSame(100, $maxPage->itemsPerPage);
    }
}
