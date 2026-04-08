<?php

declare(strict_types=1);

namespace App\Domain\City\Model;

use DateTimeImmutable;

use function is_array;
use function is_string;

final readonly class City
{
    public function __construct(
        public string $inseeCode,
        public string $name,
        public string $departmentCode,
        public string $regionCode,
        public string $postalCode,
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
        $postalCodes = $raw['codesPostaux'] ?? [];
        $firstPostalCode = is_array($postalCodes) && [] !== $postalCodes ? $postalCodes[0] : '';

        return new self(
            inseeCode: is_string($code) ? $code : '',
            name: is_string($nom) ? $nom : '',
            departmentCode: is_string($dept) ? $dept : '',
            regionCode: is_string($region) ? $region : '',
            postalCode: is_string($firstPostalCode) ? $firstPostalCode : '',
            createdAt: $now,
            updatedAt: $now,
        );
    }
}
