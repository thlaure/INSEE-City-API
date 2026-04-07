<?php

declare(strict_types=1);

namespace App\Domain\City\Handler;

use App\Domain\City\DTO\ImportResultDTO;
use App\Domain\City\Model\City;
use App\Domain\City\Port\CityDataProviderInterface;
use App\Domain\City\Port\CityRepositoryInterface;

final readonly class ImportCitiesHandler
{
    public function __construct(
        private CityDataProviderInterface $dataProvider,
        private CityRepositoryInterface $cityRepository,
    ) {
    }

    public function __invoke(): ImportResultDTO
    {
        $rawCities = $this->dataProvider->fetchAllCommunes();
        $created = 0;
        $updated = 0;
        $batchCount = 0;
        foreach ($rawCities as $raw) {
            $isNew = $this->cityRepository->save(City::fromGeoApiData($raw));
            $isNew ? ++$created : ++$updated;

            if (++$batchCount % 50 === 0) {
                $this->cityRepository->flush();
            }
        }
        $this->cityRepository->flush();

        return new ImportResultDTO(
            created: $created,
            updated: $updated,
            totalProcessed: $created + $updated,
        );
    }
}
