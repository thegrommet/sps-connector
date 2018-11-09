<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Invoice Line element
 */
class InvoiceLine extends AbstractItem implements ExportsXmlInterface
{
    public $invoiceQty;
    public $invoiceQtyUOM;
    public $purchasePrice;
    public $purchasePriceBasis;
    public $shipQty;
    public $shipQtyUOM;
    public $qtyLeftToReceive;

    public function __construct()
    {
        $this->invoiceQtyUOM = self::UOM_EACH;
        $this->purchasePriceBasis = self::PRICE_BASIS_EACH;
        $this->shipQtyUOM = self::UOM_EACH;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if ($this->invoiceQtyUOM != self::UOM_EACH || $this->shipQtyUOM != self::UOM_EACH) {
            throw new ElementInvalid('InvoiceLine: UOM attributes must be in EA.');
        }
        $root = $parent->addChild('InvoiceLine');
        $this->addChild($root, 'LineSequenceNumber', $this->formatSequenceNumber());
        $this->addChild($root, 'BuyerPartNumber', $this->buyerPartNumber, false);
        $this->addChild($root, 'VendorPartNumber', $this->vendorPartNumber);
        $this->addChild($root, 'ConsumerPackageCode', $this->consumerPackageCode);
        $this->addChild($root, 'InvoiceQty', (string)$this->invoiceQty, false);
        $this->addChild($root, 'InvoiceQtyUOM', $this->invoiceQtyUOM, false);
        $this->addChild($root, 'PurchasePrice', (string)round($this->purchasePrice, 2));
        $this->addChild($root, 'PurchasePriceBasis', $this->purchasePriceBasis);
        $this->addChild($root, 'ShipQty', (string)$this->shipQty);
        $this->addChild($root, 'ShipQtyUOM', $this->shipQtyUOM);
        $this->addChild($root, 'QtyLeftToReceive', (string)$this->qtyLeftToReceive, false);
        return $root;
    }

    protected function addChild(SimpleXMLElement $parent, string $name, ?string $value, bool $required = true): void
    {
        if ($required && ($value === '' || $value === null)) {
            throw new ElementNotSet(sprintf('InvoiceLine: %s must be set.', $name));
        }
        if ($value !== '' && $value !== null) {
            $parent->addChild($name, $value);
        }
    }
}
