<?php

declare(strict_types=1);

namespace App\Domain\City\Model;

use InvalidArgumentException;

final readonly class CitySearchCriteria
{
    public function __construct(
        public ?string $name = null,
        public ?string $departmentCode = null,
        public ?string $regionCode = null,
        public int $page = 1,
        public int $itemsPerPage = 30,
    ) {
        if ($page < 1) {
            throw new InvalidArgumentException('Page must be >= 1.');
        }

        if ($itemsPerPage < 1 || $itemsPerPage > 100) {
            throw new InvalidArgumentException('Items per page must be between 1 and 100.');
        }
    }
}
