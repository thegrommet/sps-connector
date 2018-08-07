<?php
declare(strict_types=1);

namespace Tests\Sftp;

use phpseclib\Net\SFTP;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use SpsConnector\Sftp\Client;
use SpsConnector\Sftp\Exception\LoginFailed;
use SpsConnector\Sftp\Exception\ServerError;

/**
 * SFTP Client Test Suite
 */
class ClientTest extends TestCase
{
    public function testLoginSuccess(): void
    {
        $client = $this->client();
        $this->assertTrue($client->login('a', 'b'));
    }

    public function testLoginFailure(): void
    {
        $this->expectException(LoginFailed::class);
        $client = $this->client(false);
        $client->login();
    }

    public function testGetSuccess(): void
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('get')
            ->with($this->stringContains('.'))
            ->willReturn('file contents');

        $this->assertEquals('file contents', $client->get('test.txt'));
    }

    public function testGetEmptyResponse(): void
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('get')
            ->with($this->stringContains('.'))
            ->willReturn('');

        $this->assertEquals('', $client->get('test.txt'));
    }

    public function testGetInvalidResponse(): void
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

    public function testPutSuccess(): void
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('put')
            ->with($this->stringContains('.'))
            ->willReturn(true);

        $this->assertTrue($client->put('test.txt', '--data--'));
    }

    public function testPutFailure(): void
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('put')
            ->with($this->stringContains('.'))
            ->willReturn(false);

        $this->assertFalse($client->put('test.txt', '--data--'));
    }

    public function testDeleteSuccess(): void
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('delete')
            ->with($this->stringContains('.'))
            ->willReturn(true);

        $this->assertTrue($client->delete('test.txt'));
    }

    public function testDeleteFailure(): void
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('delete')
            ->with($this->stringContains('.'))
            ->willReturn(false);

        $this->assertFalse($client->delete('test.txt'));
    }

    public function testLsSuccess(): void
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('nlist')
            ->willReturn(['.', '..', 'my-dir', 'my-file.txt']);

        $this->assertCount(2, $client->ls());
        $this->assertCount(4, $client->ls('.', true));
    }

    public function testLsFailure(): void
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

    public function testChdirSuccess(): void
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('chdir')
            ->willReturn(true);

        $this->assertTrue($client->chdir('test'));
    }

    public function testChdirFailure(): void
    {
        $client = $this->client();
        $mockSftp = $client->getClient();
        $mockSftp
            ->method('chdir')
            ->willReturn(false);

        $this->assertFalse($client->chdir('test'));
    }

    public function testLogger(): void
    {
        $client = $this->client();
        $logger = new ClientLogger();
        $client->setLogger($logger);
        $client->login('test', 'secret');
        $client->log('debug message', 'debug');
        $this->assertEquals([
            ['info' => 'CMD login test ***'],
            ['debug' => 'debug message']
        ], $logger->logs);
    }

    private function client(bool $loginResult = true): Client
    {
        $mockSftp = $this->getMockBuilder(SFTP::class)
            ->setMethods(['login', 'get', 'put', 'delete', 'chdir', 'nlist'])
            ->setConstructorArgs(['test.com'])
            ->getMock();

        $mockSftp
            ->expects($this->exactly(1))
            ->method('login')
            ->willReturn($loginResult);

        $client = new Client('test.com', 'a', 'b');
        $client->setClient($mockSftp);
        return $client;
    }
}

class ClientLogger extends AbstractLogger
{
    public $logs = [];

    public function log($level, $message, array $context = []): void
    {
        $this->logs[] = [$level => $message];
    }
}
