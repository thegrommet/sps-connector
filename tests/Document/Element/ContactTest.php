<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\Contact;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * Contact Element Test Suite
 */
class ContactTest extends TestCase
{
    public function testImportFromXml(): void
    {
        $xml = new SimpleXMLElement('<Contacts>
            <ContactTypeCode>RE</ContactTypeCode>
            <ContactName>John Doe</ContactName>
            <PrimaryPhone>123-456-7890</PrimaryPhone>
            <PrimaryEmail>john@retailer.com</PrimaryEmail>
        </Contacts>');

        $contact = new Contact();
        $contact->importFromXml($xml);

        $this->assertEquals(Contact::TYPE_DELIVERY, $contact->typeCode);
        $this->assertEquals('John Doe', $contact->name);
        $this->assertEquals('123-456-7890', $contact->phone);
        $this->assertEquals('john@retailer.com', $contact->email);
    }

    public function testExportToXml(): void
    {
        $contact = $this->contact();
        $xml = new SimpleXMLElement('<contact/>');
        $contact->exportToXml($xml);
        $this->assertEquals(Contact::TYPE_DELIVERY, (string)$xml->Contacts->ContactTypeCode);
        $this->assertEquals('John Doe', (string)$xml->Contacts->ContactName);
        $this->assertEquals('123-456-7890', (string)$xml->Contacts->PrimaryPhone);
        $this->assertEquals('john@retailer.com', (string)$xml->Contacts->PrimaryEmail);
    }

    public function testExportToXmlInvalidType(): void
    {
        $contact = $this->contact();
        $contact->typeCode = 'BAD';
        $xml = new SimpleXMLElement('<contact/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Invalid type code.');
        $contact->exportToXml($xml);
    }

    private function contact(): Contact
    {
        $contact = new Contact();
        $contact->typeCode = Contact::TYPE_DELIVERY;
        $contact->name = 'John Doe';
        $contact->phone = '123-456-7890';
        $contact->email = 'john@retailer.com';

        return $contact;
    }
}
