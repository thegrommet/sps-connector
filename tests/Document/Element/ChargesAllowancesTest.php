<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\ChargesAllowances;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * ChargesAllowances Element Test Suite
 */
class ChargesAllowancesTest extends TestCase
{
    public function testExportToXml(): void
    {
        $pd = new ChargesAllowances('C', 'D240', 20, '05', 'Freight');
        $xml = new SimpleXMLElement('<root/>');
        $pd->exportToXml($xml);
        $this->assertSame('C', (string)$xml->ChargesAllowances->AllowChrgIndicator);
        $this->assertSame('D240', (string)$xml->ChargesAllowances->AllowChrgCode);
        $this->assertSame('20', (string)$xml->ChargesAllowances->AllowChrgAmt);
        $this->assertSame('05', (string)$xml->ChargesAllowances->AllowChrgHandlingCode);
        $this->assertSame('Freight', (string)$xml->ChargesAllowances->AllowChrgHandlingDescription);
    }

    public function testExportToXmlInvalidIndicator(): void
    {
        $qaw = new ChargesAllowances('D', 'D240', 20);
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('ChargesAllowances: Invalid AllowChrgIndicator.');
        $qaw->exportToXml($xml);
    }
}
