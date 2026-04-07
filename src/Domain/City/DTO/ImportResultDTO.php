<?php

declare(strict_types=1);

namespace App\Domain\City\DTO;

final readonly class ImportResultDTO
{
    public function __construct(
        public int $created,
        public int $updated,
        public int $totalProcessed,
    ) {
    }
}
