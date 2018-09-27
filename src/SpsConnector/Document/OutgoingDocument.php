<?php
declare(strict_types=1);

namespace SpsConnector\Document;

use Exception;
use SpsConnector\Document\Element\DateTimeTrait;
use SpsConnector\Sftp\Client;
use SpsConnector\Sftp\Exception\CommandFailed;

/**
 * Outgoing Document - those uploaded to SPS
 */
abstract class OutgoingDocument extends AbstractDocument
{
    use DateTimeTrait;
    use XmlBuilderTrait {
        __construct as traitConstruct;
    }

    public function __construct(Client $client = null)
    {
        parent::__construct($client);
        $this->traitConstruct();
    }

    /**
     * Upload this document to the FTP.
     *
     * @param string $fileName
     * @param string $remoteDirectory
     * @return bool
     */
    public function uploadDocument(string $fileName, string $remoteDirectory = 'in'): bool
    {
        if (!$this->sftp) {
            throw new Exception('SFTP client has not been set.');
        }
        $result = $this->sftp->chdir('/' . ltrim($remoteDirectory, '/'));
        if (!$result) {
            throw new CommandFailed('Could not change to remote directory.');
        }
        return $this->sftp->put($fileName, $this->__toString());
    }
}
