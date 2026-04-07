<?php

declare(strict_types=1);

namespace App\Domain\City\Handler;

use App\Domain\City\Model\CityCollection;
use App\Domain\City\Model\CitySearchCriteria;
use App\Domain\City\Port\CityRepositoryInterface;
use App\Domain\City\Query\SearchCitiesQuery;

final readonly class SearchCitiesHandler
{
    public function __construct(
        private CityRepositoryInterface $cityRepository,
    ) {
    }

    public function __invoke(SearchCitiesQuery $query): CityCollection
    {
        return $this->cityRepository->findByCriteria(new CitySearchCriteria(
            name: $query->name,
            departmentCode: $query->departmentCode,
            regionCode: $query->regionCode,
            page: $query->page,
            itemsPerPage: $query->itemsPerPage,
        ));
    }
}
