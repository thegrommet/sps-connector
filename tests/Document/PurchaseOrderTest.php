<?php
declare(strict_types=1);

namespace Tests\Document;

use phpseclib\Net\SFTP;
use PHPUnit\Framework\TestCase;
use SpsConnector\Document\PurchaseOrder;
use SpsConnector\Sftp\Client;

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
        $this->assertEquals('alt@spscommerce.com', (string)$contact->PrimaryEmail);
    }

    public function testAddressByType(): void
    {
        $document = $this->document();
        $this->assertNull($document->addressByType('NM'));
        $contact = $document->addressByType('BT');
        $this->assertEquals('Corporate Headquarters', (string)$contact->AddressName);
    }

    public function testCombineNotes(): void
    {
        $document = $this->document();
        $this->assertEquals("General Note: FOR QUESTIONS PLEASE CONTACT YOUR BUYER\nCustomization: Note 2", $document->combineNotes());
        $this->assertEquals("General Note: FOR QUESTIONS PLEASE CONTACT YOUR BUYER - Customization: Note 2", $document->combineNotes(' - '));
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
