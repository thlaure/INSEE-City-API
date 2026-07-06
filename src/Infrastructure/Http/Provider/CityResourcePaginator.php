<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Provider;

use ApiPlatform\State\Pagination\PaginatorInterface;
use App\Entity\City;
use App\UI\ApiResource\CityResource;

/**
 * @implements \IteratorAggregate<mixed, CityResource>
 * @implements PaginatorInterface<CityResource>
 */
final readonly class CityResourcePaginator implements \IteratorAggregate, PaginatorInterface
{
    /**
     * @param PaginatorInterface<City> $entities
     */
    public function __construct(
        private PaginatorInterface $entities,
    ) {
    }

    public function count(): int
    {
        return $this->entities->count();
    }

    public function getLastPage(): float
    {
        return $this->entities->getLastPage();
    }

    public function getTotalItems(): float
    {
        return $this->entities->getTotalItems();
    }

    public function getCurrentPage(): float
    {
        return $this->entities->getCurrentPage();
    }

    public function getItemsPerPage(): float
    {
        return $this->entities->getItemsPerPage();
    }

    public function getIterator(): \Generator
    {
        foreach ($this->entities as $entity) {
            yield CityResource::fromEntity($entity);
        }
    }
}
