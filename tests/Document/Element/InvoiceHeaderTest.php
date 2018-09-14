<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\InvoiceHeader;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * InvoiceHeader Element Test Suite
 */
class InvoiceHeaderTest extends TestCase
{
    public function testExportToXml(): void
    {
        $header = $this->header();
        $xml = new SimpleXMLElement('<root/>');
        $header->exportToXml($xml);
        $this->assertSame('TRD123', (string)$xml->InvoiceHeader->TradingPartnerId);
        $this->assertSame('INV-123', (string)$xml->InvoiceHeader->InvoiceNumber);
        $this->assertSame('2018-08-12', (string)$xml->InvoiceHeader->InvoiceDate);
        $this->assertSame('2018-08-10', (string)$xml->InvoiceHeader->PurchaseOrderDate);
        $this->assertSame('PO-123', (string)$xml->InvoiceHeader->PurchaseOrderNumber);
        $this->assertSame('NS', (string)$xml->InvoiceHeader->PrimaryPOTypeCode);
        $this->assertSame('DR', (string)$xml->InvoiceHeader->InvoiceTypeCode);
        $this->assertSame('USD', (string)$xml->InvoiceHeader->BuyersCurrency);
        $this->assertSame('SPS', (string)$xml->InvoiceHeader->Department);
        $this->assertSame('4443', (string)$xml->InvoiceHeader->Vendor);
        $this->assertSame('70000111', (string)$xml->InvoiceHeader->BillOfLadingNumber);
        $this->assertSame('70000111', (string)$xml->InvoiceHeader->CarrierProNumber);
        $this->assertSame('2018-08-11', (string)$xml->InvoiceHeader->ShipDate);
    }

    public function testExportToXmlRequired(): void
    {
        $header = $this->header();
        $header->shipDate = null;
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementNotSet::class);
        $this->expectExceptionMessage('InvoiceHeader: ShipDate must be set.');
        $header->exportToXml($xml);
    }

    private function header(): InvoiceHeader
    {
        $header = new InvoiceHeader();
        $header->tradingPartnerId = 'TRD123';
        $header->invoiceNumber = 'INV-123';
        $header->invoiceDate = '2018-08-12';
        $header->purchaseOrderDate = '2018-08-10';
        $header->purchaseOrderNumber = 'PO-123';
        $header->primaryPOTypeCode = 'NS';
        $header->invoiceTypeCode = 'DR';
        $header->buyersCurrency = 'USD';
        $header->department = 'SPS';
        $header->vendor = '4443';
        $header->carrierPro = '70000111';
        $header->billOfLading = '70000111';
        $header->shipDate = '2018-08-11';

        return $header;
    }
}
