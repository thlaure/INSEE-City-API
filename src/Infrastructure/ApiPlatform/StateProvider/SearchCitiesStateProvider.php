<?php

declare(strict_types=1);

namespace App\Infrastructure\ApiPlatform\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\City\Handler\SearchCitiesHandler;
use App\Domain\City\Query\SearchCitiesQuery;
use App\Infrastructure\ApiPlatform\Paginator\CityPaginator;

/**
 * @implements ProviderInterface<CityPaginator>
 */
final readonly class SearchCitiesStateProvider implements ProviderInterface
{
    public function __construct(
        private SearchCitiesHandler $searchCitiesHandler,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): CityPaginator
    {
        /** @var array<string, string> $filters */
        $filters = $context['filters'] ?? [];

        $page = isset($filters['page']) ? max(1, (int) $filters['page']) : 1;
        $itemsPerPage = isset($filters['itemsPerPage']) ? max(1, min(100, (int) $filters['itemsPerPage'])) : 30;

        $query = new SearchCitiesQuery(
            name: $filters['name'] ?? null,
            departmentCode: $filters['departmentCode'] ?? null,
            regionCode: $filters['regionCode'] ?? null,
            page: $page,
            itemsPerPage: $itemsPerPage,
        );

        $collection = ($this->searchCitiesHandler)($query);

        return new CityPaginator(
            collection: $collection,
            currentPage: $page,
            itemsPerPage: $itemsPerPage,
        );
    }
}
