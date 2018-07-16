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
    public function testGetEdiType()
    {
        $document = new PurchaseOrder();
        $this->assertEquals(850, $document->getEdiType());
    }

    public function testFetchNewDocuments()
    {
        $document = $this->document();
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
            ->willReturn(['.', '..', 'PR12345', 'PR123456.xml', 'NOTAPO.xml', 'PR1234567.xml', 'pr4321']);
        $mockClient
            ->expects($this->exactly(3))
            ->method('get')
            ->willReturn($xml);

        $this->assertEquals(
            ['PR12345' => $xml, 'PR123456.xml' => $xml, 'PR1234567.xml' => $xml],
            $document->fetchNewDocuments()
        );
    }

    private function document(): PurchaseOrder
    {
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
        return $document;
    }
}
