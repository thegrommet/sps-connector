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
        $this->orderedQtyUOM = self::PRICE_BASIS_EACH;
        $this->shipQtyUOM = self::PRICE_BASIS_EACH;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if ($this->orderedQtyUOM != self::PRICE_BASIS_EACH || $this->shipQtyUOM != self::PRICE_BASIS_EACH) {
            throw new ElementInvalid('UOM attributes must be in EA.');
        }
        if ($this->itemStatusCode != self::ITEM_STATUS_ACCEPT
            && $this->itemStatusCode != self::ITEM_STATUS_ACCEPT_SHIP) {
            throw new ElementInvalid('Invalid item status code.');
        }
        $root = $parent->addChild('ShipmentLine');
        $root->addChild('LineSequenceNumber', $this->formatSequenceNumber());
        $root->addChild('BuyerPartNumber', $this->buyerPartNumber);
        $root->addChild('VendorPartNumber', $this->vendorPartNumber);
        $root->addChild('ConsumerPackageCode', $this->consumerPackageCode);
        $root->addChild('OrderQty', (string)$this->orderedQty);
        $root->addChild('OrderQtyUOM', $this->orderedQtyUOM);
        $root->addChild('ItemStatusCode', $this->itemStatusCode);
        $root->addChild('ShipQty', (string)$this->shipQty);
        $root->addChild('ShipQtyUOM', $this->shipQtyUOM);
        return $root;
    }
}
