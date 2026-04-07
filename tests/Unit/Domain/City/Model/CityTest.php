<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\City\Model;

use App\Domain\City\Model\City;
use PHPUnit\Framework\TestCase;

final class CityTest extends TestCase
{
    public function testFromGeoApiDataMapsAllFields(): void
    {
        $raw = [
            'code' => '75056',
            'nom' => 'Paris',
            'codeDepartement' => '75',
            'codeRegion' => '11',
            'population' => 2133111,
        ];

        $city = City::fromGeoApiData($raw);

        $this->assertSame('75056', $city->inseeCode);
        $this->assertSame('Paris', $city->name);
        $this->assertSame('75', $city->departmentCode);
        $this->assertSame('11', $city->regionCode);
        $this->assertSame(2133111, $city->population);
    }

    public function testFromGeoApiDataWithNullPopulation(): void
    {
        $raw = [
            'code' => '01001',
            'nom' => 'L\'Abergement-Clémenciat',
            'codeDepartement' => '01',
            'codeRegion' => '84',
        ];

        $city = City::fromGeoApiData($raw);

        $this->assertNull($city->population);
    }

    public function testFromGeoApiDataWithMissingFieldsUsesEmptyDefaults(): void
    {
        $city = City::fromGeoApiData([]);

        $this->assertSame('', $city->inseeCode);
        $this->assertSame('', $city->name);
        $this->assertSame('', $city->departmentCode);
        $this->assertSame('', $city->regionCode);
        $this->assertNull($city->population);
    }
}
