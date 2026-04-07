<?php

declare(strict_types=1);

namespace App\Infrastructure\ApiPlatform\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Domain\City\Handler\SearchCitiesHandler;
use App\Domain\City\Query\SearchCitiesQuery;
use App\Infrastructure\ApiPlatform\Input\CitySearchFilters;
use App\Infrastructure\ApiPlatform\Paginator\CityPaginator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @implements ProviderInterface<CityPaginator>
 */
final readonly class SearchCitiesStateProvider implements ProviderInterface
{
    public function __construct(
        private SearchCitiesHandler $searchCitiesHandler,
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): CityPaginator
    {
        /** @var array<string, string> $rawFilters */
        $rawFilters = $context['filters'] ?? [];

        $filters = new CitySearchFilters();
        $filters->name = $rawFilters['name'] ?? null;
        $filters->departmentCode = $rawFilters['departmentCode'] ?? null;
        $filters->regionCode = $rawFilters['regionCode'] ?? null;
        $filters->page = isset($rawFilters['page']) ? (int) $rawFilters['page'] : 1;
        $filters->itemsPerPage = isset($rawFilters['itemsPerPage']) ? (int) $rawFilters['itemsPerPage'] : 30;

        $violations = $this->validator->validate($filters);

        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }

        $query = new SearchCitiesQuery(
            name: $filters->name,
            departmentCode: $filters->departmentCode,
            regionCode: $filters->regionCode,
            page: $filters->page,
            itemsPerPage: $filters->itemsPerPage,
        );

        $collection = ($this->searchCitiesHandler)($query);

        return new CityPaginator(
            collection: $collection,
            currentPage: $filters->page,
            itemsPerPage: $filters->itemsPerPage,
        );
    }
}
