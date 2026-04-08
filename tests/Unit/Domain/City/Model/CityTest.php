<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\City\Model;

use App\Domain\City\Model\City;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CityTest extends TestCase
{
    public function testConstructorCreatesCityWithValidData(): void
    {
        $city = new City(
            inseeCode: '75056',
            name: 'Paris',
            departmentCode: '75',
            regionCode: '11',
            postalCode: '75001',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );

        $this->assertSame('75056', $city->inseeCode);
        $this->assertSame('Paris', $city->name);
        $this->assertSame('75', $city->departmentCode);
        $this->assertSame('11', $city->regionCode);
        $this->assertSame('75001', $city->postalCode);
    }

    public function testConstructorAllowsEmptyPostalCode(): void
    {
        $city = new City(
            inseeCode: '01001',
            name: 'L\'Abergement-Clemenciat',
            departmentCode: '01',
            regionCode: '84',
            postalCode: '',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );

        $this->assertSame('', $city->postalCode);
    }

    public function testConstructorRejectsEmptyInseeCode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('City INSEE code must not be empty.');

        new City(
            inseeCode: '',
            name: 'Paris',
            departmentCode: '75',
            regionCode: '11',
            postalCode: '75001',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );
    }

    public function testConstructorRejectsEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('City name must not be empty.');

        new City(
            inseeCode: '75056',
            name: '',
            departmentCode: '75',
            regionCode: '11',
            postalCode: '75001',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );
    }
}
