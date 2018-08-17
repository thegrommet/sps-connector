<?php
declare(strict_types=1);

namespace SpsConnector\Document;

use SpsConnector\Sftp\Client;

/**
 * Abstract Document
 */
abstract class AbstractDocument
{
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i:sP';

    /**
     * @var Client
     */
    protected $sftp;

    public function __construct(Client $client = null)
    {
        if ($client) {
            $this->setSftpClient($client);
        }
    }

    public function setSftpClient(Client $client): self
    {
        $this->sftp = $client;
        return $this;
    }

    public function getSftpClient(): Client
    {
        return $this->sftp;
    }
}
