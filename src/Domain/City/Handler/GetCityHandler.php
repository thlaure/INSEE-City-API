<?php

declare(strict_types=1);

namespace App\Domain\City\Handler;

use App\Domain\City\Exception\CityNotFoundException;
use App\Domain\City\Model\City;
use App\Domain\City\Port\CityRepositoryInterface;
use App\Domain\City\Query\GetCityQuery;

final readonly class GetCityHandler
{
    public function __construct(
        private CityRepositoryInterface $cityRepository,
    ) {
    }

    public function __invoke(GetCityQuery $query): City
    {
        $city = $this->cityRepository->findByInseeCode($query->inseeCode);

        if (!$city instanceof \App\Domain\City\Model\City) {
            throw CityNotFoundException::forInseeCode($query->inseeCode);
        }

        return $city;
    }
}
