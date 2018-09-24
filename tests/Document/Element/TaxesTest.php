<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\Taxes;

/**
 * Taxes Element Test Suite
 */
class TaxesTest extends TestCase
{
    public function testExportToXml(): void
    {
        $tax = new Taxes(3.50);
        $xml = new SimpleXMLElement('<root/>');
        $tax->exportToXml($xml);
        $this->assertSame('3.5', (string)$xml->Taxes->TaxAmount);
        $this->assertSame('GS', (string)$xml->Taxes->TaxTypeCode);
    }
}
