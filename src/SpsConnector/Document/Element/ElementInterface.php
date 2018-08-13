<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;

/**
 * Element Interface
 */
interface ElementInterface
{
    /**
     * Add this element's information to the given XML parent.
     *
     * @param SimpleXMLElement $parent
     * @return SimpleXMLElement The added element under $parent
     */
    public function addToXml(SimpleXMLElement $parent): SimpleXMLElement;
}
