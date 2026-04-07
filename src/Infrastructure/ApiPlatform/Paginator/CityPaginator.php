<?php

declare(strict_types=1);

namespace App\Infrastructure\ApiPlatform\Paginator;

use ApiPlatform\State\Pagination\PaginatorInterface;
use App\Domain\City\Model\City;
use App\Domain\City\Model\CityCollection;
use App\Infrastructure\ApiPlatform\Resource\CityResource;
use ArrayIterator;

use function ceil;
use function count;

use IteratorAggregate;

/**
 * @implements IteratorAggregate<int, CityResource>
 * @implements PaginatorInterface<CityResource>
 *
 * @phpstan-ignore generics.interfaceConflict
 */
final readonly class CityPaginator implements IteratorAggregate, PaginatorInterface
{
    public function __construct(
        private CityCollection $collection,
        private int $currentPage,
        private int $itemsPerPage,
    ) {
    }

    public function getCurrentPage(): float
    {
        return (float) $this->currentPage;
    }

    public function getLastPage(): float
    {
        if ($this->itemsPerPage === 0 || $this->collection->getTotalCount() === 0) {
            return 1.0;
        }

        return ceil($this->collection->getTotalCount() / $this->itemsPerPage);
    }

    public function getItemsPerPage(): float
    {
        return (float) $this->itemsPerPage;
    }

    public function getTotalItems(): float
    {
        return (float) $this->collection->getTotalCount();
    }

    public function count(): int
    {
        return count($this->collection->getItems());
    }

    /**
     * @return ArrayIterator<int, CityResource>
     */
    public function getIterator(): ArrayIterator
    {
        $resources = [];

        foreach ($this->collection->getItems() as $city) {
            /* @var City $city */
            $resources[] = new CityResource(
                inseeCode: $city->inseeCode,
                name: $city->name,
                departmentCode: $city->departmentCode,
                regionCode: $city->regionCode,
                population: $city->population,
            );
        }

        return new ArrayIterator($resources);
    }
}
