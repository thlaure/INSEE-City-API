<?php

declare(strict_types=1);

namespace App\Domain\City\Exception;

use DomainException;

use function sprintf;

final class CityNotFoundException extends DomainException
{
    public static function forInseeCode(string $inseeCode): self
    {
        return new self(sprintf('City with INSEE code "%s" was not found.', $inseeCode));
    }
}
