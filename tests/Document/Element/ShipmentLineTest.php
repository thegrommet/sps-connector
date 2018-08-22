<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\LineItem;
use SpsConnector\Document\Element\ShipmentLine;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * ShipmentLine Element Test Suite
 */
class ShipmentLineTest extends TestCase
{
    public function testExportToXml(): void
    {
        $line = new ShipmentLine();
        $line->sequenceNumber = 1;
        $line->sequenceNumberLength = 2;
        $line->buyerPartNumber = 'buy123';
        $line->vendorPartNumber = 'ven123';
        $line->consumerPackageCode = 'con123';
        $line->orderedQty = 40;
        $line->orderedQtyUOM = 'EA';
        $line->itemStatusCode = $line::ITEM_STATUS_ACCEPT_SHIP;
        $line->shipQty = 40;

        $xml = new SimpleXMLElement('<root/>');
        $line->exportToXml($xml);
        $this->assertSame('01', (string)$xml->ShipmentLine->LineSequenceNumber);
        $this->assertSame('buy123', (string)$xml->ShipmentLine->BuyerPartNumber);
        $this->assertSame('ven123', (string)$xml->ShipmentLine->VendorPartNumber);
        $this->assertSame('con123', (string)$xml->ShipmentLine->ConsumerPackageCode);
        $this->assertSame(40, (int)$xml->ShipmentLine->OrderQty);
        $this->assertSame('EA', (string)$xml->ShipmentLine->OrderQtyUOM);
        $this->assertSame($line::ITEM_STATUS_ACCEPT_SHIP, (string)$xml->ShipmentLine->ItemStatusCode);
        $this->assertSame(40, (int)$xml->ShipmentLine->ShipQty);
        $this->assertSame('EA', (string)$xml->ShipmentLine->ShipQtyUOM);
    }

    public function testFormatSequenceNumber(): void
    {
        $line = new ShipmentLine();
        $line->sequenceNumber = 2;
        $this->assertSame('2', $line->formatSequenceNumber());
        $line->sequenceNumberLength = 3;
        $this->assertSame('002', $line->formatSequenceNumber());
        $line->sequenceNumber = 10;
        $this->assertSame('010', $line->formatSequenceNumber());
        $line->sequenceNumberLength = 1;
        $this->assertSame('10', $line->formatSequenceNumber());
    }

    public function testExportToXmlInvalidUOM(): void
    {
        $line = new ShipmentLine();
        $line->shipQtyUOM = 'BAD';
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('UOM attributes must be in EA.');
        $line->exportToXml($xml);
    }

    public function testCopyFromPOItem(): void
    {
        $poItem = new LineItem();
        $poItem->sequenceNumber = 1;
        $poItem->sequenceNumberLength = 2;
        $poItem->buyerPartNumber = 'buy123';
        $poItem->vendorPartNumber = 'ven123';
        $poItem->consumerPackageCode = 'con123';
        $poItem->orderedQty = 40;
        $poItem->orderedQtyUOM = 'EA';

        $shipmentItem = new ShipmentLine();
        $shipmentItem->copyFromPOItem($poItem);
        $this->assertSame('01', $shipmentItem->formatSequenceNumber());
        $this->assertSame('buy123', $shipmentItem->buyerPartNumber);
        $this->assertSame('ven123', $shipmentItem->vendorPartNumber);
        $this->assertSame('con123', $shipmentItem->consumerPackageCode);
        $this->assertSame(40, $shipmentItem->orderedQty);
        $this->assertSame($poItem::UOM_EACH, $shipmentItem->orderedQtyUOM);
    }
}
