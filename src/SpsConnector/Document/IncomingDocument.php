<?php
declare(strict_types=1);

namespace SpsConnector\Document;

use Exception;
use SimpleXMLElement;
use SpsConnector\Exception\CommandFailed;

/**
 * Incoming Document - those downloaded from SPS
 */
abstract class IncomingDocument extends AbstractDocument
{
    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * Fetches and returns an array of documents from the FTP.
     *
     * @param int $limit
     * @param string $remoteDirectory
     * @param bool $deleteAfterFetch
     * @return static[]
     */
    public function fetchNewDocuments(int $limit = -1, string $remoteDirectory = 'out', bool $deleteAfterFetch = true): array
    {
        if (!$this->sftp) {
            throw new Exception('SFTP client has not been set.');
        }
        $result = $this->sftp->chdir($remoteDirectory);
        if (!$result) {
            throw new CommandFailed('Could not change to remote directory.');
        }
        $limited = $limit !== null && $limit > 0;
        $orders = [];
        $listing = $this->sftp->ls();
        foreach ($listing as $fileName) {
            if (!$this->documentTypeCode() || strpos($fileName, $this->documentTypeCode()) === 0) {
                $orders[] = $fileName;
                if ($limited && --$limit <= 0) {
                    break;
                }
            }
        }
        $documents = [];
        foreach ($orders as $order) {
            $document = new static();
            $document->setXml($this->sftp->get($order));
            $documents[$order] = $document;
        }
        if ($deleteAfterFetch) {
            foreach ($orders as $order) {
                $this->sftp->delete($order);
            }
        }
        return $documents;
    }

    public function documentTypeCode(): string
    {
        return '';
    }

    /**
     * @param SimpleXMLElement|string $xml
     * @return $this
     */
    public function setXml($xml): self
    {
        if ($xml instanceof SimpleXMLElement) {
            $xml = $xml->asXml();  // remove any connection to an existing document
        } elseif (!is_string($xml)) {
            throw new \TypeError('Invalid type for XML parameter.');
        }
        // rename non-prefixed namespaces, which aren't supported with xpath()
        $xml = str_replace('xmlns=', 'ns=', $xml);
        $this->xml = new SimpleXMLElement($xml);
        return $this;
    }

    public function getXml(): SimpleXMLElement
    {
        if ($this->xml === null) {
            throw new Exception('XML has not been set.');
        }
        return $this->xml;
    }

    /**
     * Return the value of the first matched element specified by xpath. If the element has children rather than a
     * value, an empty string is returned.
     *
     * @param string $xpath
     * @return string
     */
    public function getXmlData(string $xpath): string
    {
        $xml = $this->getXml();
        $data = $xml->xpath($xpath);
        if (is_array($data) && count($data)) {
            $first = $data[0];
            if ($first->count()) {
                return '';
            }
            return (string)$first;
        }
        return '';
    }

    /**
     * Return the child elements of the element specified by xpath.
     *
     * @param string $xpath
     * @return SimpleXMLElement[]
     */
    public function getXmlElements(string $xpath): array
    {
        $xml = $this->getXml();
        $data = $xml->xpath($xpath);
        if (is_array($data)) {
            return $data;
        }
        return [];
    }
}
