<?php
declare(strict_types=1);

namespace SpsConnector\Document;

use Exception;
use SpsConnector\Exception\CommandFailed;
use SpsConnector\Sftp\Client;

/**
 * Purchase Order EDI document
 */
class PurchaseOrder implements DocumentInterface
{
    const EDI_TYPE = 850;

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

    public function getEdiType(): int
    {
        return self::EDI_TYPE;
    }

    public function fetchNewDocuments(string $remoteDirectory = 'in', bool $deleteAfterFetch = true): array
    {
        if (!$this->sftp) {
            throw new Exception('SFTP client has not been set.');
        }
        $result = $this->sftp->chdir($remoteDirectory);
        if (!$result) {
            throw new CommandFailed('Could not change to remote directory.');
        }
        $orders = [];
        $listing = $this->sftp->ls();
        foreach ($listing as $fileName) {
            if (strpos($fileName, 'PR') === 0) {
                $orders[] = $fileName;
            }
        }
        $documents = [];
        foreach ($orders as $order) {
            $documents[$order] = $this->sftp->get($order);
        }
        if ($deleteAfterFetch) {
            foreach ($orders as $order) {
                $this->sftp->delete($order);
            }
        }
        return $documents;
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
