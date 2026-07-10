<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\City\Model;

use App\Domain\City\Model\CountryCode;
use PHPUnit\Framework\TestCase;

final class CountryCodeTest extends TestCase
{
    public function testFromResolvesKnownIsoCode(): void
    {
        $this->assertSame(CountryCode::FR, CountryCode::from('FR'));
        $this->assertSame(CountryCode::DE, CountryCode::from('DE'));
    }

    public function testFromRejectsUnknownCode(): void
    {
        $this->expectException(\ValueError::class);

        CountryCode::from('XX');
    }

    public function testTryFromReturnsNullForUnknownCode(): void
    {
        $this->assertNull(CountryCode::tryFrom('XX'));
    }
}
