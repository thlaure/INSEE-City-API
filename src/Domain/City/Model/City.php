<?php

declare(strict_types=1);

namespace App\Domain\City\Model;

final readonly class City
{
    public function __construct(
        public string $inseeCode,
        public string $name,
        public string $departmentCode,
        public string $regionCode,
        public ?string $postalCode,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt,
    ) {
        if ('' === trim($inseeCode)) {
            throw new \InvalidArgumentException('City INSEE code must not be empty.');
        }

        if ('' === trim($name)) {
            throw new \InvalidArgumentException('City name must not be empty.');
        }

        if ('' === trim($departmentCode)) {
            throw new \InvalidArgumentException('City department code must not be empty.');
        }

        if ('' === trim($regionCode)) {
            throw new \InvalidArgumentException('City region code must not be empty.');
        }
    }
}
