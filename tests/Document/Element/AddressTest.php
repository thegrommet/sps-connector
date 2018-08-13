<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\Address;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Address Element Test Suite
 */
class AddressTest extends TestCase
{
    public function testAddToXml(): void
    {
        $address = $this->address();
        $xml = new SimpleXMLElement('<address/>');
        $address->addToXml($xml);
        $this->assertEquals(Address::TYPE_SHIP_FROM, (string)$xml->Address->AddressTypeCode);
        $this->assertEquals('012', (string)$xml->Address->AddressLocationNumber);
        $this->assertEquals('Main Warehouse', (string)$xml->Address->AddressName);
        $this->assertEquals('123 Main', (string)$xml->Address->Address1);
        $this->assertEquals('Suite B', (string)$xml->Address->Address2);
        $this->assertEquals('Boulder', (string)$xml->Address->City);
        $this->assertEquals('CO', (string)$xml->Address->State);
        $this->assertEquals('80301', (string)$xml->Address->PostalCode);
        $this->assertEquals('USA', (string)$xml->Address->Country);
    }

    public function testAddToXmlRequired(): void
    {
        $address = $this->address();
        $address->name = '';
        $xml = new SimpleXMLElement('<address/>');
        $this->expectException(ElementNotSet::class);
        $this->expectExceptionMessage('Element "AddressName" is required in an address.');
        $address->addToXml($xml);
    }

    public function testAddToXmlInvalidType(): void
    {
        $address = $this->address();
        $address->typeCode = 'BAD';
        $xml = new SimpleXMLElement('<address/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Invalid type code.');
        $address->addToXml($xml);
    }

    private function address(): Address
    {
        $address = new Address();
        $address->typeCode = Address::TYPE_SHIP_FROM;
        //$address->locationQualifier = 'todo';
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
