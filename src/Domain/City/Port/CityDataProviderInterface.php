<?php

declare(strict_types=1);

namespace App\Domain\City\Port;

interface CityDataProviderInterface
{
    /**
     * Fetch all communes from the external data source.
     *
     * @return array<array<string, mixed>>
     */
    public function fetchAllCommunes(): array;
}
