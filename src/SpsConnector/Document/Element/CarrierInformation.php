<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * CarrierInformation
 */
class CarrierInformation implements ExportsXmlInterface
{
    /**
     * 2-4 character SCAC
     *
     * @var string
     */
    public $carrierAlphaCode;

    /**
     * Carrier name
     *
     * @var string
     */
    public $carrierRouting;

    public function __construct(string $carrierAlphaCode = null, string $carrierRouting = null)
    {
        $this->carrierAlphaCode = $carrierAlphaCode;
        $this->carrierRouting = $carrierRouting;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if (!$this->carrierAlphaCode || !$this->carrierRouting) {
            throw  new ElementNotSet('Both "carrierAlphaCode" and "carrierRouting" must be set.');
        }
        $root = $parent->addChild('CarrierInformation');
        $root->addChild('CarrierAlphaCode', $this->carrierAlphaCode);
        $root->addChild('CarrierRouting', $this->carrierRouting);
        return $root;
    }
}
