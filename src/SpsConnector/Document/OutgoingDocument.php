<?php
declare(strict_types=1);

namespace SpsConnector\Document;

use Exception;
use SimpleXMLElement;
use SpsConnector\Sftp\Client;
use SpsConnector\Sftp\Exception\CommandFailed;

/**
 * Outgoing Document - those uploaded to SPS
 */
abstract class OutgoingDocument extends AbstractDocument
{
    const XMLNS = 'http://www.spscommerce.com/RSX';

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    public function __construct(Client $client = null)
    {
        parent::__construct($client);
        $this->xml = new SimpleXMLElement('<' . $this->rootElementName() . '/>', 0, false, self::XMLNS);
    }

    /**
     * Returns the XML's root element name.
     *
     * @return string
     */
    abstract public function rootElementName(): string;

    /**
     * Add child to the root or add a hierarchy by separating elements with "/", eg. "node/node2".
     * The added node is returned.
     *
     * @param string $name
     * @param string|SimpleXMLElement $value
     * @return SimpleXMLElement
     */
    public function addElement(string $name, $value = null): SimpleXMLElement
    {
        if (strpos($name, '/') !== false) {
            $elements = explode('/', $name);
            $last = array_pop($elements);
            $root = $this->xml;
            foreach ($elements as $elementName) {
                foreach ($root->children() as $child) {
                    if ($child->getName() == $elementName) {
                        $root = $child;
                        continue(2);
                    }
                }
                $root = $root->addChild($elementName);
            }
            return $root->addChild($last, $value);
        } else {
            return $this->xml->addChild($name, $value);
        }
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
        $result = $this->sftp->chdir($remoteDirectory);
        if (!$result) {
            throw new CommandFailed('Could not change to remote directory.');
        }
        return $this->sftp->put($fileName, $this->__toString());
    }

    public function __toString(): string
    {
        return $this->xml->asXML();
    }
}
