<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Physical Details element
 */
class PhysicalDetails implements ExportsXmlInterface
{
    const MEDIUM_CARTON       = 'CTN';
    const WEIGHT_LB           = 'LB';
    const VOLUME_CUBIC_FEET   = 'CF';
    const VOLUME_CUBIC_INCHES = 'CI';

    public $packingMedium;
    public $weight;
    public $weightUOM;
    public $volume;
    public $volumeUOM;

    public function __construct(
        float $weight = null,
        float $volume = null,
        string $weightUOM = self::WEIGHT_LB,
        string $volumeUOM = self::VOLUME_CUBIC_INCHES,
        string $packingMedium = null
    ) {
        $this->weight = $weight;
        $this->weightUOM = $weightUOM;
        $this->volume = $volume;
        $this->volumeUOM = $volumeUOM;
        $this->packingMedium = $packingMedium;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if (!$this->weight || !$this->volume) {
            throw new ElementNotSet('PhysicalDetails: Both PackWeight and PackVolume must be set.');
        }
        if ($this->weightUOM != self::WEIGHT_LB) {
            throw new ElementInvalid('PhysicalDetails: Invalid PackWeightUOM.');
        }
        if ($this->volumeUOM != self::VOLUME_CUBIC_INCHES && $this->volumeUOM != self::VOLUME_CUBIC_FEET) {
            throw new ElementInvalid('PhysicalDetails: Invalid PackVolumeUOM.');
        }
        $root = $parent->addChild('PhysicalDetails');
        if ($this->packingMedium) {
            if ($this->packingMedium != self::MEDIUM_CARTON) {
                throw new ElementInvalid('PhysicalDetails: Invalid packing medium.');
            }
            $root->addChild('PackingMedium', $this->packingMedium);
        }
        $root->addChild('PackWeight', (string)round($this->weight, 2));
        $root->addChild('PackWeightUOM', $this->weightUOM);
        $root->addChild('PackVolume', (string)round($this->volume, 2));
        $root->addChild('PackVolumeUOM', $this->volumeUOM);
        return $root;
    }
}
