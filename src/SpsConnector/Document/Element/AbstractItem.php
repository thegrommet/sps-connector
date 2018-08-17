<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

/**
 * Abstract Item element
 */
abstract class AbstractItem
{
    const PRICE_BASIS_EACH = 'PE';

    public $sequenceNumber;
    public $buyerPartNumber;
    public $vendorPartNumber;
    public $consumerPackageCode;
    public $orderedQty;
    public $orderedQtyUOM;

    /**
     * @var int
     */
    public $sequenceNumberLength = 1;

    /**
     * The line sequence # can be like 1, 2, ... or 001, 002, ... and that format should be maintained.
     *
     * @return string
     */
    public function formatSequenceNumber(): string
    {
        return str_pad((string)$this->sequenceNumber, $this->sequenceNumberLength, '0', STR_PAD_LEFT);
    }
}
