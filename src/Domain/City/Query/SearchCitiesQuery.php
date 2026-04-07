<?php

declare(strict_types=1);

namespace App\Domain\City\Query;

final readonly class SearchCitiesQuery
{
    public function __construct(
        public ?string $name = null,
        public ?string $departmentCode = null,
        public ?string $regionCode = null,
        public int $page = 1,
        public int $itemsPerPage = 30,
    ) {
    }
}
