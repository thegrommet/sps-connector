<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\InvoiceLine;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * InvoiceLine Element Test Suite
 */
class InvoiceLineTest extends TestCase
{
    public function testExportToXml(): void
    {
        $line = new InvoiceLine();
        $line->sequenceNumber = 1;
        $line->sequenceNumberLength = 2;
        $line->buyerPartNumber = 'buy123';
        $line->vendorPartNumber = 'ven123';
        $line->consumerPackageCode = 'con123';
        $line->invoiceQty = 40;
        $line->invoiceQtyUOM = 'EA';
        $line->purchasePrice = 13.99;
        $line->shipQty = 40;
        $line->qtyLeftToReceive = 2;

        $xml = new SimpleXMLElement('<root/>');
        $line->exportToXml($xml);
        $this->assertSame('01', (string)$xml->InvoiceLine->LineSequenceNumber);
        $this->assertSame('buy123', (string)$xml->InvoiceLine->BuyerPartNumber);
        $this->assertSame('ven123', (string)$xml->InvoiceLine->VendorPartNumber);
        $this->assertSame('con123', (string)$xml->InvoiceLine->ConsumerPackageCode);
        $this->assertSame(40, (int)$xml->InvoiceLine->InvoiceQty);
        $this->assertSame('EA', (string)$xml->InvoiceLine->InvoiceQtyUOM);
        $this->assertSame(13.99, (float)$xml->InvoiceLine->PurchasePrice);
        $this->assertSame('PE', (string)$xml->InvoiceLine->PurchasePriceBasis);
        $this->assertSame(40, (int)$xml->InvoiceLine->ShipQty);
        $this->assertSame('EA', (string)$xml->InvoiceLine->ShipQtyUOM);
        $this->assertSame(2, (int)$xml->InvoiceLine->QtyLeftToReceive);
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
}
