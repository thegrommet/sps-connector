<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * Shipment Line element
 */
class ShipmentLine extends AbstractItem implements ExportsXmlInterface
{
    const ITEM_STATUS_ACCEPT      = 'IA';
    const ITEM_STATUS_ACCEPT_SHIP = 'AC';

    public $itemStatusCode;
    public $shipQty;
    public $shipQtyUOM;

    public function __construct()
    {
        $this->orderedQtyUOM = self::UOM_EACH;
        $this->shipQtyUOM = self::UOM_EACH;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if ($this->orderedQtyUOM != self::UOM_EACH || $this->shipQtyUOM != self::UOM_EACH) {
            throw new ElementInvalid('ShipmentLine: UOM attributes must be in EA.');
        }
        if ($this->itemStatusCode != self::ITEM_STATUS_ACCEPT
            && $this->itemStatusCode != self::ITEM_STATUS_ACCEPT_SHIP) {
            throw new ElementInvalid('ShipmentLine: Invalid ItemStatusCode.');
        }
        $root = $parent->addChild('ShipmentLine');
        $root->addChild('LineSequenceNumber', $this->formatSequenceNumber());
        $root->addChild('BuyerPartNumber', $this->buyerPartNumber);
        $root->addChild('VendorPartNumber', $this->vendorPartNumber);
        if ($this->consumerPackageCode) {
            $root->addChild('ConsumerPackageCode', $this->consumerPackageCode);
        }
        $root->addChild('OrderQty', (string)$this->orderedQty);
        $root->addChild('OrderQtyUOM', $this->orderedQtyUOM);
        $root->addChild('ItemStatusCode', $this->itemStatusCode);
        $root->addChild('ShipQty', (string)$this->shipQty);
        $root->addChild('ShipQtyUOM', $this->shipQtyUOM);
        return $root;
    }

    /**
     * Copies required data from the PO line item to this shipment item.
     *
     * @param OrderLineItem $poItem
     */
    public function copyFromPOItem(OrderLineItem $poItem): void
    {
        $this->sequenceNumber = $poItem->sequenceNumber;
        $this->sequenceNumberLength = $poItem->sequenceNumberLength;
        $this->buyerPartNumber = $poItem->buyerPartNumber;
        $this->vendorPartNumber = $poItem->vendorPartNumber;
        $this->consumerPackageCode = $poItem->consumerPackageCode;
        $this->orderedQty = $poItem->orderedQty;
        $this->orderedQtyUOM = $poItem->orderedQtyUOM;
    }
}
