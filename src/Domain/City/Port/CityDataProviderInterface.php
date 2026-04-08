<?php

declare(strict_types=1);

namespace App\Domain\City\Port;

interface CityDataProviderInterface
{
    /**
     * Fetch all cities from the external data source.
     *
     * @return array<array<string, mixed>>
     */
    public function fetchAllCities(): array;
}
