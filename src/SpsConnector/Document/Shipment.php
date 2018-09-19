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

    /**
     * Returns the Address SimpleXMLElement matching the given type code, if it exists.
     *
     * @param string $type
     * @return null|SimpleXMLElement
     */
    public function addressXmlByType(string $type): ?SimpleXMLElement
    {
        foreach ($this->getXmlElements('Shipment/Header/Address') as $address) {
            if ($address->AddressTypeCode == $type) {
                return $address;
            }
        }
        return null;
    }

    /*
     * Convenience methods for commonly-added elements
     */

    public function addHeaderDate(Date $date): SimpleXMLElement
    {
        return $date->exportToXml($this->header());
    }

    public function addHeaderReference(Reference $reference): SimpleXMLElement
    {
        return $reference->exportToXml($this->header());
    }

    public function addHeaderAddress(Address $address): SimpleXMLElement
    {
        return $address->exportToXml($this->header());
    }
}
