<?php
declare(strict_types=1);

namespace Tests\Document;

use phpseclib\Net\SFTP;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\IncomingDocument;
use SpsConnector\Sftp\Client;

/**
 * IncomingDocumentTest
 */
class IncomingDocumentTest extends TestCase
{
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
            ->willReturn(['.', '..', 'TD12345', 'TD123456.xml', 'NOTATD.xml', 'TD1234567.xml', 'td4321']);
        $mockClient
            ->expects($this->exactly(6))
            ->method('get')
            ->willReturn($xml);

        $documents = $document->fetchNewDocuments();
        $this->assertSame(
            ['TD12345', 'TD123456.xml', 'TD1234567.xml'],
            array_keys($documents)
        );
        $this->assertInstanceOf(IncomingDocImpl::class, $documents['TD12345']);
        $this->assertCount(2, $document->fetchNewDocuments(2, 'out', false));
        $this->assertCount(1, $document->fetchNewDocuments(1, 'out', false));
    }

    public function testGetSetXml(): void
    {
        $document = new IncomingDocImpl();
        $xml = '<?xml version="1.0" encoding="utf-8"?><Orders xmlns="http://www.spscommerce.com/RSX"><Header/></Orders>';
        $expected = '<?xml version="1.0" encoding="utf-8"?><Orders ns="http://www.spscommerce.com/RSX"><Header/></Orders>';
        $document->setXml($xml);
        $this->assertInstanceOf(SimpleXMLElement::class, $document->getXml());
        $this->assertSame($expected, str_replace("\n", '', $document->getXml()->asXML()));

        $document->setXml(new SimpleXMLElement($xml));
        $this->assertSame($expected, str_replace("\n", '', $document->getXml()->asXML()));
    }

    public function testGetXmlNotSet(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('XML has not been set.');
        $document = new IncomingDocImpl();
        $document->getXml();
    }

    public function testSetXmlInvalid(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Invalid type for XML parameter.');
        $document = new IncomingDocImpl();
        $document->setXml(0);
    }

    public function testGetXmlData(): void
    {
        $document = $this->document();
        $this->assertSame('525', $document->getXmlData('//Order/Header/OrderHeader/TradingPartnerId'));
        $this->assertSame('', $document->getXmlData('//Order/Header/OrderHeader'));
        $this->assertSame('1', $document->getXmlData('//Order/LineItem/OrderLine/LineSequenceNumber'));
    }

    public function testGetXmlChildren(): void
    {
        $document = $this->document();
        $this->assertEquals(
            [new SimpleXMLElement('<data>525</data>')],
            $document->getXmlElements('//Order/Header/OrderHeader/TradingPartnerId')
        );
        $header = $document->getXmlElements('//Order/Header/OrderHeader');
        $this->assertCount(10, $header[0]->children());
    }

    private function document(bool $mockClient = false): IncomingDocImpl
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

            $document = new IncomingDocImpl($client);
        } else {
            $document = new IncomingDocImpl();
        }
        $document->setXml(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'PurchaseOrderTest.xml'));
        return $document;
    }
}

class IncomingDocImpl extends IncomingDocument
{
    public function ediNumber(): int
    {
        return 700;
    }

    public function documentTypeCode(): string
    {
        return 'TD';
    }
}