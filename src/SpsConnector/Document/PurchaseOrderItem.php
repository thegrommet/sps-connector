<?php
declare(strict_types=1);

namespace SpsConnector\Document;

/**
 * Purchase Order Item
 */
class PurchaseOrderItem extends IncomingDocument
{
    const PRICE_BASIS_EACH = 'PE';

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
     * Is the line being priced by each'es? Assume each if the element is not present.
     *
     * @return bool
     */
    public function isPricingByEach(): bool
    {
        $priceBasis = (string)$this->getXmlData('//OrderLine/PurchasePriceBasis');
        return empty($priceBasis) || $priceBasis == self::PRICE_BASIS_EACH;
    }
}
