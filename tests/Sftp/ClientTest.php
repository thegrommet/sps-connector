<?php
declare(strict_types=1);

namespace Tests\Sftp;

use phpseclib\Net\SFTP;
use PHPUnit\Framework\TestCase;
use SpsConnector\Sftp\Client;
use SpsConnector\Sftp\Exception\ServerError;

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

    public function testGetSuccess()
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('get')
            ->with($this->stringContains('.'))
            ->willReturn('file contents');

        $this->assertEquals('file contents', $client->get('test.txt'));
    }

    public function testGetEmptyResponse()
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('get')
            ->with($this->stringContains('.'))
            ->willReturn('');

        $this->assertEquals('', $client->get('test.txt'));
    }

    public function testGetInvalidResponse()
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('get')
            ->with($this->stringContains('.'))
            ->willReturn(false);

        $this->expectException(ServerError::class);
        $this->expectExceptionMessage('Invalid response');

        $client->get('test.txt');
    }

    public function testPutSuccess()
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('put')
            ->with($this->stringContains('.'))
            ->willReturn(true);

        $this->assertTrue($client->put('test.txt', '--data--'));
    }

    public function testPutFailure()
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('put')
            ->with($this->stringContains('.'))
            ->willReturn(false);

        $this->assertFalse($client->put('test.txt', '--data--'));
    }

    public function testDeleteSuccess()
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('delete')
            ->with($this->stringContains('.'))
            ->willReturn(true);

        $this->assertTrue($client->delete('test.txt'));
    }

    public function testDeleteFailure()
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('delete')
            ->with($this->stringContains('.'))
            ->willReturn(false);

        $this->assertFalse($client->delete('test.txt'));
    }

    public function testLsSuccess()
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('nlist')
            ->willReturn(['.', '..', 'my-dir', 'my-file.txt']);

        $this->assertCount(2, $client->ls());
        $this->assertCount(4, $client->ls('.', true));
    }

    public function testLsFailure()
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('nlist')
            ->willReturn(false);

        $this->expectException(ServerError::class);
        $this->expectExceptionMessage('Unable to retrieve directory listing');

        $client->ls();
    }

    private function client(): Client
    {
        $mockSftp = $this->getMockBuilder(SFTP::class)
            ->setMethods(['login', 'get', 'put', 'delete', 'nlist'])
            ->setConstructorArgs(['test.com'])
            ->getMock();

        $mockSftp
            ->expects($this->exactly(1))
            ->method('login')
            ->willReturn(true);

        $client = new Client('test.com', 'a', 'b');
        $client->setClient($mockSftp);
        return $client;
    }
}
