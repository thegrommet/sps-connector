<?php
declare(strict_types=1);

namespace Tests\Document;

use phpseclib\Net\SFTP;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\PurchaseOrder;
use SpsConnector\Sftp\Client;

/**
 * Purchase Order Doc Test Suite
 */
class PurchaseOrderTest extends TestCase
{
    public function testGetEdiType(): void
    {
        $document = new PurchaseOrder();
        $this->assertEquals(850, $document->getEdiType());
    }

    public function testFetchNewDocuments(): void
    {
        $document = $this->document(true);
        $sftp = $document->getSftpClient();
        $mockClient = $sftp->getClient();

        $xml = '<?xml version="1.0" encoding="utf-8"?><Orders xmlns="http://www.spscommerce.com/RSX"/>';

        $mockClient
            ->expects($this->exactly(3))
            ->method('delete')
            ->willReturn(true);
        $mockClient
            ->method('chdir')
            ->willReturn(true);
        $mockClient
            ->method('nlist')
            ->willReturn(['.', '..', 'PO12345', 'PO123456.xml', 'NOTAPO.xml', 'PO1234567.xml', 'po4321']);
        $mockClient
            ->expects($this->exactly(6))
            ->method('get')
            ->willReturn($xml);

        $documents = $document->fetchNewDocuments();
        $this->assertEquals(
            ['PO12345', 'PO123456.xml', 'PO1234567.xml'],
            array_keys($documents)
        );
        $this->assertInstanceOf(PurchaseOrder::class, $documents['PO12345']);
        $this->assertCount(2, $document->fetchNewDocuments(2, 'out', false));
        $this->assertCount(1, $document->fetchNewDocuments(1, 'out', false));
    }

    public function testGetSetXml(): void
    {
        $document = new PurchaseOrder();
        $xml = '<?xml version="1.0" encoding="utf-8"?><Orders xmlns="http://www.spscommerce.com/RSX"><Header/></Orders>';
        $expected = '<?xml version="1.0" encoding="utf-8"?><Orders ns="http://www.spscommerce.com/RSX"><Header/></Orders>';
        $document->setXml($xml);
        $this->assertInstanceOf(SimpleXMLElement::class, $document->getXml());
        $this->assertEquals($expected, str_replace("\n", '', $document->getXml()->asXML()));

        $document->setXml(new SimpleXMLElement($xml));
        $this->assertEquals($expected, str_replace("\n", '', $document->getXml()->asXML()));
    }

    public function testGetXmlNotSet(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('XML has not been set.');
        $document = new PurchaseOrder();
        $document->getXml();
    }

    public function testSetXmlInvalid(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Invalid type for XML parameter.');
        $document = new PurchaseOrder();
        $document->setXml(0);
    }

    public function testGetXmlData(): void
    {
        $document = $this->document();
        $this->assertEquals('525', $document->getXmlData('//Order/Header/OrderHeader/TradingPartnerId'));
        $this->assertEquals('', $document->getXmlData('//Order/Header/OrderHeader'));
        $this->assertEquals('1', $document->getXmlData('//Order/LineItem/OrderLine/LineSequenceNumber'));
    }

    public function testGetXmlChildren(): void
    {
        $document = $this->document();
        $this->assertEquals(
            [new SimpleXMLElement('<data>525</data>')],
            $document->getXmlElements('//Order/Header/OrderHeader/TradingPartnerId')
        );
        $header = $document->getXmlElements('//Order/Header/OrderHeader');
        $this->assertCount(11, $header[0]->children());
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

    private function document(bool $mockClient = false): PurchaseOrder
    {
        if ($mockClient) {
            $mockSftp = $this->getMockBuilder(SFTP::class)
                ->setMethods(['login', 'get', 'chdir', 'delete', 'nlist'])
                ->setConstructorArgs(['test.com'])
                ->getMock();

            $mockSftp
                ->expects($this->exactly(1))
                ->method('login')
                ->willReturn(true);

            $client = new Client('test.com', 'a', 'b');
            $client->setClient($mockSftp);

            $document = new PurchaseOrder($client);
        }
        else {
            $document = new PurchaseOrder();
        }
        $document->setXml(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'PurchaseOrderTest.xml'));
        return $document;
    }
}
