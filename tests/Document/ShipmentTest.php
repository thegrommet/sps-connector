<?php
declare(strict_types=1);

namespace Tests\Document;

use PHPUnit\Framework\TestCase;
use SpsConnector\Document\Element\Address;
use SpsConnector\Document\Element\Date;
use SpsConnector\Document\Element\Reference;
use SpsConnector\Document\Shipment;

/**
 * Shipment Doc Test Suite
 */
class ShipmentTest extends TestCase
{
    public function testEdiNumber(): void
    {
        $document = new Shipment();
        $this->assertSame(856, $document->ediNumber());
    }

    public function testAddHeaderDate(): void
    {
        $document = new Shipment();
        $document->addHeaderDate(new Date('011', '2018-01-02'));
        $document->addHeaderDate(new Date('017', '2018-01-05'));

        $expected = '<?xml version="1.0"?>' .
            '<Shipments>' .
            '<Shipment>' .
            '<Header>' .
            '<Dates>' .
            '<DateTimeQualifier>011</DateTimeQualifier>' .
            '<Date>2018-01-02</Date>' .
            '</Dates>' .
            '<Dates>' .
            '<DateTimeQualifier>017</DateTimeQualifier>' .
            '<Date>2018-01-05</Date>' .
            '</Dates>' .
            '</Header>' .
            '</Shipment>' .
            '</Shipments>';

        $this->assertSame($expected, str_replace("\n", '', $document->__toString()));
    }

    public function testAddHeaderReference(): void
    {
        $document = new Shipment();
        $document->addHeaderReference(new Reference('LO', 'asdf'));
        $document->addHeaderReference(new Reference('MK', '1234'));

        $expected = '<?xml version="1.0"?>' .
            '<Shipments>' .
            '<Shipment>' .
            '<Header>' .
            '<References>' .
            '<ReferenceQual>LO</ReferenceQual>' .
            '<ReferenceID>asdf</ReferenceID>' .
            '</References>' .
            '<References>' .
            '<ReferenceQual>MK</ReferenceQual>' .
            '<ReferenceID>1234</ReferenceID>' .
            '</References>' .
            '</Header>' .
            '</Shipment>' .
            '</Shipments>';

        $this->assertSame($expected, str_replace("\n", '', $document->__toString()));
    }

    public function testAddHeaderAddress(): void
    {
        $document = new Shipment();
        $address = $document->addHeaderAddress($this->address());
        $this->assertSame('Main Warehouse', (string)$address->AddressName);
    }

    public function testAddressXmlByType(): void
    {
        $document = new Shipment();
        $address = $this->address();
        $document->addHeaderAddress($address);
        $address->typeCode = Address::TYPE_BUYING_PARTY;
        $document->addHeaderAddress($address);
        $this->assertInstanceOf(\SimpleXMLElement::class, $document->addressXmlByType(Address::TYPE_SHIP_FROM));
        $this->assertInstanceOf(\SimpleXMLElement::class, $document->addressXmlByType(Address::TYPE_BUYING_PARTY));
        $this->assertNull($document->addressXmlByType('NM'));
    }

    private function address(): Address
    {
        $address = new Address();
        $address->typeCode = Address::TYPE_SHIP_FROM;
        $address->locationNumber = '012';
        $address->name = 'Main Warehouse';
        $address->street1 = '123 Main';
        $address->street2 = 'Suite B';
        $address->city = 'Boulder';
        $address->state = 'CO';
        $address->postalCode = '80301';

        return $address;
    }
}
