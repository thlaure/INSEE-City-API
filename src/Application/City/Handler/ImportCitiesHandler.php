<?php

declare(strict_types=1);

namespace App\Application\City\Handler;

use App\Application\City\DTO\ImportResultDTO;
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
        $cities = $this->dataProvider->fetchAllCities();
        $created = 0;
        $updated = 0;
        $batchCount = 0;

        foreach ($cities as $city) {
            $isNew = $this->cityRepository->save($city);
            $isNew ? ++$created : ++$updated;

            if (0 === ++$batchCount % 50) {
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
