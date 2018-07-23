<?php
declare(strict_types=1);

namespace SpsConnector\Document;

/**
 * Purchase Order Item
 */
class PurchaseOrderItem extends AbstractDocument
{
    public function price(): float
    {
        return (float)$this->getXmlData('//OrderLine/PurchasePrice');
    }

    public function qty(): int
    {
        return (int)$this->getXmlData('//OrderLine/OrderQty');
    }

    public function rowTotal(): float
    {
        return $this->price() * $this->qty();
    }

    /**
     * Compare ordered qty UOM with the price basis UOM and return true if equal.
     *
     * @return bool
     */
    public function comparePricingUOM(): bool
    {
        return (string)$this->getXmlData('//OrderLine/OrderQtyUOM') === (string)$this->getXmlData('//OrderLine/PurchasePriceBasis');
    }
}
