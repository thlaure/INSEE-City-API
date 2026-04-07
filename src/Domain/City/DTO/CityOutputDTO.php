<?php

declare(strict_types=1);

namespace App\Domain\City\DTO;

use App\Domain\City\Model\City;

final readonly class CityOutputDTO
{
    public function __construct(
        public string $inseeCode,
        public string $name,
        public string $departmentCode,
        public string $regionCode,
        public ?int $population,
    ) {
    }

    public static function fromDomainModel(City $city): self
    {
        return new self(
            inseeCode: $city->inseeCode,
            name: $city->name,
            departmentCode: $city->departmentCode,
            regionCode: $city->regionCode,
            population: $city->population,
        );
    }
}
