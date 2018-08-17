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
        $this->assertSame('<?xml version="1.0"?><Test/>', str_replace("\n", '', $document->__toString()));
    }
    
    public function testAddElement(): void
    {
        $document = new OutgoingDocImpl();
        $toString = function () use ($document) {
            return str_replace("\n", '', $document->__toString());
        };
        $document->addElement('Shipment');
        $this->assertSame(
            '<?xml version="1.0"?><Test><Shipment/></Test>',
            $toString()
        );
        $document->addElement('Shipment');
        $this->assertSame(
            '<?xml version="1.0"?><Test><Shipment/><Shipment/></Test>',
            $toString()
        );
        $document->addElement('A', '1');
        $this->assertSame(
            '<?xml version="1.0"?><Test><Shipment/><Shipment/><A>1</A></Test>',
            $toString()
        );
        $document->addElement('Shipment/B');
        $this->assertSame(
            '<?xml version="1.0"?><Test><Shipment><B/></Shipment><Shipment/><A>1</A></Test>',
            $toString()
        );
        $document->addElement('Shipment/C', '2');
        $this->assertSame(
            '<?xml version="1.0"?><Test><Shipment><B/><C>2</C></Shipment><Shipment/><A>1</A></Test>',
            $toString()
        );
        $document->addElement('Shipment/D/E', '3');
        $this->assertSame(
            '<?xml version="1.0"?><Test><Shipment><B/><C>2</C><D><E>3</E></D></Shipment><Shipment/><A>1</A></Test>',
            $toString()
        );
    }

    public function testHasNode(): void
    {
        $document = new OutgoingDocImpl();
        $document->addElement('A/B/C');
        $this->assertTrue($document->hasNode('A'));
        $this->assertTrue($document->hasNode('A/B'));
        $this->assertTrue($document->hasNode('A/B/C'));
        $this->assertFalse($document->hasNode('D'));
        $this->assertFalse($document->hasNode('D/E'));
        $this->assertFalse($document->hasNode('B'));
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
