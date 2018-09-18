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
