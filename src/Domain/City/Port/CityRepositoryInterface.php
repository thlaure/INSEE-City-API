<?php

declare(strict_types=1);

namespace App\Domain\City\Port;

use App\Domain\City\Model\City;
use App\Domain\City\Model\CityCollection;
use App\Domain\City\Model\CitySearchCriteria;

interface CityRepositoryInterface
{
    public function findByInseeCode(string $inseeCode): ?City;

    /**
     * Persist a city. Returns true if created, false if updated.
     */
    public function save(City $city): bool;

    /**
     * Flush pending changes to the database.
     */
    public function flush(): void;

    public function findByCriteria(CitySearchCriteria $criteria): CityCollection;
}
