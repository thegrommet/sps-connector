<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\ShipmentHeader;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * ShipmentHeader Element Test Suite
 */
class ShipmentHeaderTest extends TestCase
{
    public function testExportToXml(): void
    {
        $header = new ShipmentHeader('TRD123', '000123', '2018-08-12', '70000012345');
        $xml = new SimpleXMLElement('<root/>');
        $header->exportToXml($xml);
        $this->assertSame('TRD123', (string)$xml->ShipmentHeader->TradingPartnerId);
        $this->assertSame('000123', (string)$xml->ShipmentHeader->ShipmentIdentification);
        $this->assertSame('2018-08-12', (string)$xml->ShipmentHeader->ShipDate);
        $this->assertSame($header::TSET_CODE_ORIGINAL, (string)$xml->ShipmentHeader->TsetPurposeCode);
        $this->assertSame($header::ASN_SOPI_CODE, (string)$xml->ShipmentHeader->ASNStructureCode);
        $this->assertSame('70000012345', (string)$xml->ShipmentHeader->BillOfLadingNumber);
        $this->assertSame('70000012345', (string)$xml->ShipmentHeader->CarrierProNumber);
        $this->assertNotEmpty((string)$xml->ShipmentHeader->ShipNoticeDate);
        $this->assertNotEmpty((string)$xml->ShipmentHeader->ShipNoticeTime);
    }

    public function testExportToXmlRequired(): void
    {
        $header = new ShipmentHeader('abc', '123');
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementNotSet::class);
        $this->expectExceptionMessage('Invalid ship date.');
        $header->exportToXml($xml);
    }
}
