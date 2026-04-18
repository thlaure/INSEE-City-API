<?php

declare(strict_types=1);

namespace App\Domain\City\Exception;

final class CityDataProviderException extends \RuntimeException
{
    public static function fromPrevious(\Throwable $previous): self
    {
        return new self(
            'City data provider is unavailable: '.$previous->getMessage(),
            0,
            $previous,
        );
    }
}
