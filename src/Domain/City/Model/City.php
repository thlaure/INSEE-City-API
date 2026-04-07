<?php

declare(strict_types=1);

namespace App\Domain\City\Model;

use DateTimeImmutable;

use function is_int;
use function is_string;

final readonly class City
{
    public function __construct(
        public string $inseeCode,
        public string $name,
        public string $departmentCode,
        public string $regionCode,
        public ?int $population,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }

    /**
     * @param array<string, mixed> $raw
     */
    public static function fromGeoApiData(array $raw): self
    {
        $now = new DateTimeImmutable();
        $code = $raw['code'] ?? '';
        $nom = $raw['nom'] ?? '';
        $dept = $raw['codeDepartement'] ?? '';
        $region = $raw['codeRegion'] ?? '';
        $pop = $raw['population'] ?? null;

        return new self(
            inseeCode: is_string($code) ? $code : '',
            name: is_string($nom) ? $nom : '',
            departmentCode: is_string($dept) ? $dept : '',
            regionCode: is_string($region) ? $region : '',
            population: is_int($pop) ? $pop : null,
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
