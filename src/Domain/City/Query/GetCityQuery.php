<?php

declare(strict_types=1);

namespace App\Domain\City\Query;

final readonly class GetCityQuery
{
    public function __construct(
        public string $inseeCode,
    ) {
    }
}
