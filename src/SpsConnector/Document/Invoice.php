<?php
declare(strict_types=1);

namespace SpsConnector\Document;

use SimpleXMLElement;
use SpsConnector\Document\Element\Address;

/**
 * Invoice EDI document
 */
class Invoice extends OutgoingDocument implements DocumentInterface
{
    const EDI_NUMBER         = 810;
    const DOCUMENT_TYPE_CODE = 'IN';

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
        return 'Invoices';
    }

    /**
     * Returns the Invoice/Header element or creates it if it doesn't exist.
     *
     * @return SimpleXMLElement
     */
    protected function header(): SimpleXMLElement
    {
        if (!$this->hasNode('Invoice/Header')) {
            return $this->addElement('Invoice/Header');
        }
        return $this->xml->Invoice->Header;
    }

    public function addHeaderAddress(Address $address): SimpleXMLElement
    {
        return $address->exportToXml($this->header());
    }
}
