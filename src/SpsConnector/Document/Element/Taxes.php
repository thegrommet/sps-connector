<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Taxes element
 */
class Taxes implements ExportsXmlInterface
{
    const TYPE_GOODS_SERVICES = 'GS';

    public $type;
    public $amount;

    public function __construct(float $amount, $type = self::TYPE_GOODS_SERVICES)
    {
        $this->amount = $amount;
        $this->type = $type;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if (!$this->type) {
            throw new ElementNotSet('Taxes: TaxTypeCode must be set.');
        }
        if ($this->amount === null) {
            throw new ElementNotSet('Taxes: TaxAmount must be set.');
        }
        $root = $parent->addChild('Taxes');
        $root->addChild('TaxTypeCode', $this->type);
        $root->addChild('TaxAmount', (string)round($this->amount, 2));
        return $root;
    }
}
