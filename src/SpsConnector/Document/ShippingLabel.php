<?php
declare(strict_types=1);

namespace SpsConnector\Document;

use SimpleXMLElement;

/**
 * Shipping/GS1 Label document
 *
 * Not technically an EDI document, but it shares many similarities with outgoing SPS documents.
 */
class ShippingLabel
{
    use XmlBuilderTrait;

    public function rootElementName(): string
    {
        return 'ShippingLabels';
    }

    public function addLabel(): SimpleXMLElement
    {
        return $this->addElement('ShippingLabel');
    }
}
