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
    public function testImportFromXml(): void
    {
        $xml = new SimpleXMLElement('<Address>
                <AddressTypeCode>ST</AddressTypeCode>
                <LocationCodeQualifier>92</LocationCodeQualifier>
                <AddressLocationNumber>012</AddressLocationNumber>
                <AddressName>Main Warehouse</AddressName>
                <Address1>123 Main</Address1>
                <Address2>Suite B</Address2><!--optional-->
                <City>Boulder</City>
                <State>CO</State>
                <PostalCode>80301</PostalCode>
                <Country>USA</Country><!--optional-->
            </Address>');

        $address = new Address();
        $address->importFromXml($xml);

        $this->assertSame(Address::TYPE_SHIP_TO, $address->typeCode);
        $this->assertSame('92', $address->locationQualifier);
        $this->assertSame('012', $address->locationNumber);
        $this->assertSame('Main Warehouse', $address->name);
        $this->assertSame('123 Main', $address->street1);
        $this->assertSame('Suite B', $address->street2);
        $this->assertSame('Boulder', $address->city);
        $this->assertSame('CO', $address->state);
        $this->assertSame('80301', $address->postalCode);
        $this->assertSame('USA', $address->country);
    }

    public function testExportToXml(): void
    {
        $address = $this->address();
        $xml = new SimpleXMLElement('<address/>');
        $address->exportToXml($xml);
        $this->assertSame(Address::TYPE_SHIP_FROM, (string)$xml->Address->AddressTypeCode);
        $this->assertSame('012', (string)$xml->Address->AddressLocationNumber);
        $this->assertSame('Main Warehouse', (string)$xml->Address->AddressName);
        $this->assertSame('123 Main', (string)$xml->Address->Address1);
        $this->assertSame('Suite B', (string)$xml->Address->Address2);
        $this->assertSame('Boulder', (string)$xml->Address->City);
        $this->assertSame('CO', (string)$xml->Address->State);
        $this->assertSame('80301', (string)$xml->Address->PostalCode);
        $this->assertSame('USA', (string)$xml->Address->Country);
    }

    public function testExportToXmlRequired(): void
    {
        $address = $this->address();
        $address->name = '';
        $xml = new SimpleXMLElement('<address/>');
        $this->expectException(ElementNotSet::class);
        $this->expectExceptionMessage('Element "AddressName" is required in an address.');
        $address->exportToXml($xml);
    }

    public function testExportToXmlInvalidType(): void
    {
        $address = $this->address();
        $address->typeCode = 'BAD';
        $xml = new SimpleXMLElement('<address/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Invalid type code.');
        $address->exportToXml($xml);
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
