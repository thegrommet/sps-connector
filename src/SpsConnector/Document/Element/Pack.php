<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Pack element
 */
class Pack implements ExportsXmlInterface
{
    const TYPE_PACKAGE = 'P';
    const TYPE_PALLET  = 'T';

    public $type;
    public $shippingSerialId;

    public function __construct(string $type = null, string $shippingSerialId = null)
    {
        $this->type = $type;
        $this->shippingSerialId = $shippingSerialId;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if (!$this->type || !$this->shippingSerialId) {
            throw new ElementNotSet('Pack: Both PackLevelType and ShippingSerialID must be set.');
        }
        if ($this->type != self::TYPE_PACKAGE && $this->type != self::TYPE_PALLET) {
            throw new ElementInvalid('Pack: Invalid PackLevelType.');
        }
        $root = $parent->addChild('Pack');
        $root->addChild('PackLevelType', $this->type);
        $root->addChild('ShippingSerialID', $this->formatShippingSerialId());
        return $root;
    }

    /**
     * Format the ID to 20 chars, left padded with zeros.
     *
     * @return string
     */
    public function formatShippingSerialId(): string
    {
        return str_pad($this->shippingSerialId, 20, '0', STR_PAD_LEFT);
    }
}
