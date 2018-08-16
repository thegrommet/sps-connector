<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use SpsConnector\Scac;

/**
 * SCAC Test Suite
 */
class ScacTest extends TestCase
{
    public function testCommonCodes(): void
    {
        $scac = new Scac();
        $codes = $scac->commonCodes();
        $this->assertCount(90, $codes);
        $this->assertArrayHasKey('FDEN', $codes);
    }

    public function testNameByCode(): void
    {
        $scac = new Scac();
        $this->assertSame('FEDEX GROUND', $scac->nameByCode('FDEG'));
        $this->assertSame('United Parcel Service', $scac->nameByCode('UPSN'));
        $this->assertSame('U.S. Government', $scac->nameByCode('USAU'));
        $this->assertNull($scac->nameByCode('BOGUS'));
    }
}
