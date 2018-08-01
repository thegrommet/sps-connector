<?php
declare(strict_types=1);

namespace SpsConnector\Document;

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
}
