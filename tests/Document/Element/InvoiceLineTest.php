<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\InvoiceLine;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * InvoiceLine Element Test Suite
 */
class InvoiceLineTest extends TestCase
{
    public function testExportToXml(): void
    {
        $line = $this->invoiceLine();

        $xml = new SimpleXMLElement('<root/>');
        $line->exportToXml($xml);
        $this->assertSame('01', (string)$xml->InvoiceLine->LineSequenceNumber);
        $this->assertSame('buy123', (string)$xml->InvoiceLine->BuyerPartNumber);
        $this->assertSame('ven123', (string)$xml->InvoiceLine->VendorPartNumber);
        $this->assertSame('123456789012', (string)$xml->InvoiceLine->ConsumerPackageCode);
        $this->assertSame(40, (int)$xml->InvoiceLine->InvoiceQty);
        $this->assertSame('EA', (string)$xml->InvoiceLine->InvoiceQtyUOM);
        $this->assertSame(13.99, (float)$xml->InvoiceLine->PurchasePrice);
        $this->assertSame('PE', (string)$xml->InvoiceLine->PurchasePriceBasis);
        $this->assertSame(40, (int)$xml->InvoiceLine->ShipQty);
        $this->assertSame('EA', (string)$xml->InvoiceLine->ShipQtyUOM);
        $this->assertSame(2, (int)$xml->InvoiceLine->QtyLeftToReceive);

        $xml = new SimpleXMLElement('<root/>');
        $line->shipQty = 0;  // 0 shipped should be allowed
        $line->exportToXml($xml);
        $this->assertSame(0, (int)$xml->InvoiceLine->ShipQty);
    }

    public function testExportToXmlInvalidUOM(): void
    {
        $line = new InvoiceLine();
        $line->invoiceQtyUOM = 'BAD';
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('InvoiceLine: UOM attributes must be in EA.');
        $line->exportToXml($xml);
    }

    public function testExportToXmlRequired(): void
    {
        $line = $this->invoiceLine();
        $line->vendorPartNumber = '';
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementNotSet::class);
        $this->expectExceptionMessage('InvoiceLine: VendorPartNumber must be set.');
        $line->exportToXml($xml);
    }

    /**
     * @dataProvider invalidCPCProvider
     * @param string $cpc
     */
    public function testExportToXmlInvalidCPC(string $cpc): void
    {
        $line = new InvoiceLine();
        $line->consumerPackageCode = $cpc;
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('InvoiceLine: ConsumerPackageCode must be between 12 and 14 numeric characters.');
        $line->exportToXml($xml);
    }

    public function invalidCPCProvider(): array
    {
        return [
            ['non-numeric'],
            ['12345678901x'],
            ['12345'],  // too short
            ['1234567890123456789'],  // too long
        ];
    }

    private function invoiceLine(): InvoiceLine
    {
        $line = new InvoiceLine();
        $line->sequenceNumber = 1;
        $line->sequenceNumberLength = 2;
        $line->buyerPartNumber = 'buy123';
        $line->vendorPartNumber = 'ven123';
        $line->consumerPackageCode = '123456789012';
        $line->invoiceQty = 40;
        $line->invoiceQtyUOM = 'EA';
        $line->purchasePrice = 13.99;
        $line->shipQty = 40;
        $line->qtyLeftToReceive = 2;

        return $line;
    }
}
