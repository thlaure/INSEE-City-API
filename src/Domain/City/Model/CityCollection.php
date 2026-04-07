<?php

declare(strict_types=1);

namespace App\Domain\City\Model;

final readonly class CityCollection
{
    /**
     * @param City[] $items
     */
    public function __construct(
        private array $items,
        private int $totalCount,
    ) {
    }

    /**
     * @return City[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function isEmpty(): bool
    {
        return [] === $this->items;
    }
}
