<?php
declare(strict_types=1);

namespace Tests\Document;

use PHPUnit\Framework\TestCase;
use SpsConnector\Document\Element\Address;
use SpsConnector\Document\Element\Contact;
use SpsConnector\Document\PurchaseOrder;

/**
 * Purchase Order Doc Test Suite
 */
class PurchaseOrderTest extends TestCase
{
    public function testEdiNumber(): void
    {
        $document = new PurchaseOrder();
        $this->assertEquals(850, $document->ediNumber());
    }

    public function testPoType(): void
    {
        $document = $this->document();
        $this->assertEquals('NS', $document->poType());
    }

    public function testPoTypeDescription(): void
    {
        $document = $this->document();
        $this->assertEquals('New Store Order', $document->poTypeDescription());
    }

    public function testPoNumber(): void
    {
        $document = $this->document();
        $this->assertEquals('PO584615-1', $document->poNumber());
    }

    public function testContactByType(): void
    {
        $document = $this->document();
        $this->assertNull($document->contactByType('NM'));
        $contact = $document->contactByType('AC');
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('alt@spscommerce.com', $contact->email);
    }

    public function testContacts(): void
    {
        $document = $this->document();
        $contacts = $document->contacts();
        $this->assertCount(2, $contacts);
        $this->assertInstanceOf(Contact::class, $contacts[0]);
    }

    public function testAddressByType(): void
    {
        $document = $this->document();
        $this->assertNull($document->addressByType('NM'));
        $address = $document->addressByType('BT');
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals('Corporate Headquarters', $address->name);
    }

    public function testCombineNotes(): void
    {
        $document = $this->document();
        $this->assertEquals(
            "General Note: FOR QUESTIONS PLEASE CONTACT YOUR BUYER\nCustomization: Note 2",
            $document->combineNotes()
        );
        $this->assertEquals(
            "General Note: FOR QUESTIONS PLEASE CONTACT YOUR BUYER - Customization: Note 2",
            $document->combineNotes(' - ')
        );
    }

    public function testShippingDescription(): void
    {
        $document = $this->document();
        $this->assertEquals('J. B. Hunt - Second Day', $document->shippingDescription());
    }

    public function testPaymentTermsDescription(): void
    {
        $document = $this->document();
        $this->assertEquals('2% 30 Net 31 terms based on Invoice Date', $document->paymentTermsDescription());

        $xml = '<?xml version="1.0" encoding="utf-8"?>
<Orders xmlns="http://www.spscommerce.com/RSX">
    <Order>
        <Header>
            <PaymentTerms>
            <TermsType>03</TermsType>
            <TermsBasisDateCode>2</TermsBasisDateCode>
            <TermsDiscountDueDays>90</TermsDiscountDueDays>
            </PaymentTerms>
        </Header>
    </Order>
</Orders>';

        $document->setXml($xml);
        $this->assertEquals('Fixed Date terms based on Delivery Date', $document->paymentTermsDescription());
    }

    public function testRequestedShipDate(): void
    {
        $document = $this->document();
        $this->assertEquals('2018-05-27', $document->requestedShipDate());
    }

    private function document(): PurchaseOrder
    {
        $document = new PurchaseOrder();
        $document->setXml(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'PurchaseOrderTest.xml'));
        return $document;
    }
}
