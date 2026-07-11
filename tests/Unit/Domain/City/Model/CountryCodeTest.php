<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\City\Model;

use App\Domain\Shared\Model\CountryCode;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CountryCodeTest extends TestCase
{
    #[Test]
    public function testFromResolvesKnownIsoCode(): void
    {
        $this->assertSame(CountryCode::FR, CountryCode::from('FR'));
        $this->assertSame(CountryCode::DE, CountryCode::from('DE'));
    }

    #[Test]
    public function testFromRejectsUnknownCode(): void
    {
        $this->expectException(\ValueError::class);

        CountryCode::from('XX');
    }

    #[Test]
    public function testTryFromReturnsNullForUnknownCode(): void
    {
        $this->assertNull(CountryCode::tryFrom('XX'));
    }

    #[Test]
    public function testValuesReturnsAllIsoCodes(): void
    {
        $values = CountryCode::values();

        $this->assertContains('FR', $values);
        $this->assertContains('DE', $values);
        $this->assertCount(count(CountryCode::cases()), $values);
    }
}
