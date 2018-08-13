<?php
declare(strict_types=1);

namespace SpsConnector\Document;

use SimpleXMLElement;
use SpsConnector\Document\Element\Address;
use SpsConnector\Document\Element\Date;
use SpsConnector\Document\Element\Reference;

/**
 * Shipment EDI document
 */
class Shipment extends OutgoingDocument implements DocumentInterface
{
    const EDI_NUMBER         = 856;
    const DOCUMENT_TYPE_CODE = 'SH';

    public function ediNumber(): int
    {
        return self::EDI_NUMBER;
    }

    public function documentTypeCode(): string
    {
        return self::DOCUMENT_TYPE_CODE;
    }

    public function rootElementName(): string
    {
        return 'Shipments';
    }

    public function addShipmentHeader(array $childValues): SimpleXMLElement
    {
        $header = $this->header()->addChild('ShipmentHeader');
        foreach ($childValues as $name => $value) {
            $header->addChild($name, $value);
        }
        return $header;
    }

    /*
     * Convenience methods for commonly-added elements
     */

    public function addHeaderDate(Date $date): SimpleXMLElement
    {
        return $date->addToXml($this->header());
    }

    public function addHeaderReference(Reference $reference): SimpleXMLElement
    {
        return $reference->addToXml($this->header());
    }

    public function addHeaderAddress(Address $address): SimpleXMLElement
    {
        return $address->addToXml($this->header());
    }

    /**
     * Returns the Shipment/Header element or creates it if it doesn't exist.
     *
     * @return SimpleXMLElement
     */
    protected function header(): SimpleXMLElement
    {
        if (!$this->hasNode('Shipment/Header')) {
            return $this->addElement('Shipment/Header');
        }
        return $this->xml->Shipment->Header;
    }
}
