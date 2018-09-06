<?php
declare(strict_types=1);

namespace Tests\Document;

use PHPUnit\Framework\TestCase;
use SpsConnector\Document\Element\Address;
use SpsConnector\Document\Element\Contact;
use SpsConnector\Document\Element\Date;
use SpsConnector\Document\Element\LineItem;
use SpsConnector\Document\Element\PaymentTerms;
use SpsConnector\Document\PurchaseOrder;

/**
 * Purchase Order Doc Test Suite
 */
class PurchaseOrderTest extends TestCase
{
    public function testEdiNumber(): void
    {
        $document = new PurchaseOrder();
        $this->assertSame(850, $document->ediNumber());
    }

    public function testPoType(): void
    {
        $document = $this->document();
        $this->assertSame('NS', $document->poType());
    }

    public function testPoTypeDescription(): void
    {
        $document = $this->document();
        $this->assertSame('New Store Order', $document->poTypeDescription());
    }

    public function testPoNumber(): void
    {
        $document = $this->document();
        $this->assertSame('PO584615-1', $document->poNumber());
    }

    public function testTradingPartnerId(): void
    {
        $document = $this->document();
        $this->assertSame('525GROMM', $document->tradingPartnerId());
    }

    public function testContactByType(): void
    {
        $document = $this->document();
        $this->assertNull($document->contactByType('NM'));
        $contact = $document->contactByType('AC');
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertSame('alt@spscommerce.com', $contact->email);
    }

    public function testContacts(): void
    {
        $document = $this->document();
        $contacts = $document->contacts();
        $this->assertCount(2, $contacts);
        $this->assertInstanceOf(Contact::class, $contacts[0]);
    }

    public function testAddresses(): void
    {
        $document = $this->document();
        $addresses = $document->addresses();
        $this->assertCount(2, $addresses);
        $this->assertInstanceOf(Address::class, $addresses[0]);
    }

    public function testAddressByType(): void
    {
        $document = $this->document();
        $this->assertNull($document->addressByType('NM'));
        $address = $document->addressByType('BT');
        $this->assertInstanceOf(Address::class, $address);
        $this->assertSame('Corporate Headquarters', $address->name);
    }

    public function testPaymentTerms(): void
    {
        $document = $this->document();
        $terms = $document->paymentTerms();
        $this->assertInstanceOf(PaymentTerms::class, $terms);
        $this->assertSame('2% 30 Net 31', $terms->description);
    }

    public function testCombineNotes(): void
    {
        $document = $this->document();
        $this->assertSame(
            "General Note: FOR QUESTIONS PLEASE CONTACT YOUR BUYER\nCustomization: Note 2",
            $document->combineNotes()
        );
        $this->assertSame(
            "General Note: FOR QUESTIONS PLEASE CONTACT YOUR BUYER - Customization: Note 2",
            $document->combineNotes(' - ')
        );
    }

    public function testShippingDescription(): void
    {
        $document = $this->document();
        $this->assertSame('J. B. Hunt - Second Day', $document->shippingDescription());
    }

    public function testDates(): void
    {
        $document = $this->document();
        $dates = $document->dates();
        $this->assertCount(2, $dates);
        $this->assertInstanceOf(Date::class, $dates[0]);
    }

    public function testDateByQualifier(): void
    {
        $document = $this->document();
        $this->assertNull($document->dateByQualifier('BOGUS'));
        $date = $document->dateByQualifier('002');
        $this->assertInstanceOf(Date::class, $date);
        $this->assertSame('2017-03-15', $date->date);
    }

    public function testRequestedShipDate(): void
    {
        $document = $this->document();
        $this->assertSame('2022-05-27', $document->requestedShipDate());

        $document->setXml('<Order>
            <Header>
                <Dates>
                    <DateTimeQualifier>010</DateTimeQualifier>
                    <Date>2018-05-27</Date>
                </Dates>
            </Header>
        </Order>');

        $this->assertNull($document->requestedShipDate());
    }

    public function testItems(): void
    {
        $document = $this->document();
        $items = $document->items();
        $this->assertCount(3, $items);
        $item = current($items);
        $this->assertInstanceOf(LineItem::class, $item);
        $this->assertSame(1, $item->sequenceNumber);
    }

    private function document(): PurchaseOrder
    {
        $document = new PurchaseOrder();
        $document->setXml(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'PurchaseOrderTest.xml'));
        return $document;
    }
}
