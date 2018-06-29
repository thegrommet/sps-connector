<?php
declare(strict_types=1);

use phpseclib\Net\SFTP;
use PHPUnit\Framework\TestCase;
use SpsSftp\Client;

/**
 * ClientTest
 */
class ClientTest extends TestCase
{
    public function testLogin()
    {
        $client = $this->client();
        $this->assertTrue($client->login('a', 'b'));
    }

    private function client(): Client
    {
        $mockSftp = $this->getMockBuilder(SFTP::class)
            ->setMethods(['login'])
            ->setConstructorArgs(['test.com'])
            ->getMock();

        $mockSftp
            ->expects($this->exactly(1))
            ->method('login')
            ->willReturn(true);

        $client = new Client('test.com');
        $client->setClient($mockSftp);
        return $client;
    }
}
