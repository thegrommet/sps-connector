<?php
declare(strict_types=1);

namespace SpsConnector\Document;

use SimpleXMLElement;

/**
 * XML Builder Trait
 */
trait XmlBuilderTrait
{
    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    public function __construct()
    {
        $this->xml = new SimpleXMLElement('<' . $this->rootElementName() . '/>');
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
     * Does the XML document have a node at the path specified?
     *
     * @param string $path
     * @return bool
     */
    public function hasNode(string $path): bool
    {
        return count($this->xml->xpath($path)) > 0;
    }

    public function __toString(): string
    {
        return $this->xml->asXML();
    }
}
