<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * Quantity And Weight element
 */
class QuantityAndWeight implements ExportsXmlInterface
{
    const MEDIUM_CARTON = 'CTN';
    const WEIGHT_LB     = 'LB';

    public $packingMedium;
    public $ladingQuantity;
    public $weight;
    public $weightUOM;

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        $root = $parent->addChild('QuantityAndWeight');
        if ($this->packingMedium) {
            if ($this->packingMedium != self::MEDIUM_CARTON) {
                throw new ElementInvalid('Invalid packing medium.');
            }
            $root->addChild('PackingMedium', $this->packingMedium);
        }
        if ($this->ladingQuantity) {
            $root->addChild('LadingQuantity', (string)$this->ladingQuantity);
        }
        if ($this->weight) {
            $root->addChild('Weight', (string)round($this->weight, 2));
        }
        if ($this->weightUOM) {
            if ($this->weightUOM != self::WEIGHT_LB) {
                throw new ElementInvalid('Invalid weight UOM.');
            }
            $root->addChild('WeightUOM', $this->weightUOM);
        }
        return $root;
    }
}
