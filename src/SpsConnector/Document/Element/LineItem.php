<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * LineItem element
 */
class LineItem implements ImportsXmlInterface
{
    const PRICE_BASIS_EACH = 'PE';

    public $sequenceNumber;
    public $buyerPartNumber;
    public $vendorPartNumber;
    public $gtin;
    public $orderedQty;
    public $orderedQtyUOM;
    public $purchasePrice;
    public $purchasePriceBasis;
    public $productPartNumber;
    public $productCode;
    public $productDescription;

    /**
     * Populate this address from the given XML.
     *
     * @param SimpleXMLElement $root
     */
    public function importFromXml(SimpleXMLElement $root): void
    {
        if ($root->getName() != 'LineItem') {
            throw new ElementInvalid('LineItem root is invalid.');
        }
        $orderLine = $root->OrderLine;
        $this->sequenceNumber = (int)$orderLine->LineSequenceNumber;
        $this->buyerPartNumber = (string)$orderLine->BuyerPartNumber;
        $this->vendorPartNumber = (string)$orderLine->VendorPartNumber;
        $this->gtin = (string)$orderLine->GTIN;
        $this->orderedQty = (int)$orderLine->OrderQty;
        $this->orderedQtyUOM = (string)$orderLine->OrderQtyUOM;
        $this->purchasePrice = (float)$orderLine->PurchasePrice;
        $this->purchasePriceBasis = (string)$orderLine->PurchasePriceBasis;
        if (isset($orderLine->ProductID)) {
            $this->productPartNumber = (string)$orderLine->ProductID->PartNumber;
        }
        if (isset($root->ProductOrItemDescription)) {
            $product = $root->ProductOrItemDescription;
            $this->productCode = (string)$product->ProductCharacteristicCode;
            $this->productDescription = (string)$product->ProductDescription;
        }
    }

    public function rowTotal(): float
    {
        return $this->purchasePrice * $this->orderedQty;
    }

    /**
     * Is the line being priced by each'es? Assume each if the element is not present.
     *
     * @return bool
     */
    public function isPricingByEach(): bool
    {
        return empty($this->purchasePriceBasis) || $this->purchasePriceBasis == self::PRICE_BASIS_EACH;
    }
}
