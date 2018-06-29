<?php
declare(strict_types=1);

namespace SpsSftp;

use phpseclib\Net\SFTP;

/**
 * SFTP Client
 */
class Client
{
    /**
     * @var SFTP
     */
    protected $client;

    private $isLoggedIn = false;

    protected $host;
    protected $port;
    protected $timeout;

    public function __construct(string $host, int $port = 22, int $timeout = 10)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    public function login(string $username, string $password): bool
    {
        if (!$this->isLoggedIn) {
            $this->isLoggedIn = $this->getClient()->login($username, $password);
        }
        return $this->isLoggedIn;
    }

    public function getClient(): SFTP
    {
        if (!$this->client) {
            $this->client = new SFTP($this->host, $this->port, $this->timeout);
        }
        return $this->client;
    }

    public function setClient(SFTP $client): self
    {
        $this->client = $client;
        return $this;
    }
}
