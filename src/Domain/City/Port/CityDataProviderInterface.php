<?php

declare(strict_types=1);

namespace App\Domain\City\Port;

use App\Domain\City\Model\City;

interface CityDataProviderInterface
{
    /**
     * Fetch all cities from the external data source.
     *
     * @return iterable<City>
     */
    public function fetchAllCities(): iterable;
}
