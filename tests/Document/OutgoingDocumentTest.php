<?php
declare(strict_types=1);

namespace Tests\Document;

use phpseclib\Net\SFTP;
use PHPUnit\Framework\TestCase;
use SpsConnector\Document\OutgoingDocument;
use SpsConnector\Sftp\Client;

/**
 * Outgoing Document Test Suite
 */
class OutgoingDocumentTest extends TestCase
{
    public function testToString(): void
    {
        $document = new OutgoingDocImpl();
        $this->assertEquals('<?xml version="1.0"?><Test/>', str_replace("\n", '', $document->__toString()));
    }
    
    public function testAddElement(): void
    {
        $document = new OutgoingDocImpl();
        $toString = function () use ($document) {
            return str_replace("\n", '', $document->__toString());
        };
        $document->addElement('Shipment');
        $this->assertEquals(
            '<?xml version="1.0"?><Test><Shipment/></Test>',
            $toString()
        );
        $document->addElement('Shipment');
        $this->assertEquals(
            '<?xml version="1.0"?><Test><Shipment/><Shipment/></Test>',
            $toString()
        );
        $document->addElement('A', '1');
        $this->assertEquals(
            '<?xml version="1.0"?><Test><Shipment/><Shipment/><A>1</A></Test>',
            $toString()
        );
        $document->addElement('Shipment/B');
        $this->assertEquals(
            '<?xml version="1.0"?><Test><Shipment><B/></Shipment><Shipment/><A>1</A></Test>',
            $toString()
        );
        $document->addElement('Shipment/C', '2');
        $this->assertEquals(
            '<?xml version="1.0"?><Test><Shipment><B/><C>2</C></Shipment><Shipment/><A>1</A></Test>',
            $toString()
        );
        $document->addElement('Shipment/D/E', '3');
        $this->assertEquals(
            '<?xml version="1.0"?><Test><Shipment><B/><C>2</C><D><E>3</E></D></Shipment><Shipment/><A>1</A></Test>',
            $toString()
        );
    }
    
    public function testUploadDocument(): void
    {
        $mockSftp = $this->getMockBuilder(SFTP::class)
            ->setMethods(['login', 'put', 'chdir'])
            ->setConstructorArgs(['test.com'])
            ->getMock();

        $mockSftp
            ->expects($this->exactly(1))
            ->method('login')
            ->willReturn(true);
        $mockSftp
            ->expects($this->exactly(1))
            ->method('chdir')
            ->willReturn(true);
        $mockSftp
            ->expects($this->exactly(1))
            ->method('put')
            ->willReturn(true);

        $client = new Client('test.com', 'a', 'b');
        $client->setClient($mockSftp);
        $document = new OutgoingDocImpl($client);

        $this->assertTrue($document->uploadDocument('SH12345'));
    }
}

class OutgoingDocImpl extends OutgoingDocument
{
    public function ediNumber(): int
    {
        return 700;
    }

    public function rootElementName(): string
    {
        return 'Test';
    }
}
