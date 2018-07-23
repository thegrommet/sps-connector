<?php
declare(strict_types=1);

namespace SpsConnector\Document;

use Exception;
use SimpleXMLElement;
use SpsConnector\Sftp\Client;

/**
 * Abstract Document
 */
abstract class AbstractDocument
{
    /**
     * @var Client
     */
    protected $sftp;

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    public function setSftpClient(Client $client): self
    {
        $this->sftp = $client;
        return $this;
    }

    public function getSftpClient(): Client
    {
        return $this->sftp;
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
