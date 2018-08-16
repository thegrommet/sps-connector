<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;

/**
 * Imports XML Interface
 */
interface ImportsXmlInterface
{
    /**
     * Populate this object from the given XML.
     *
     * @param SimpleXMLElement $root
     */
    public function importFromXml(SimpleXMLElement $root): void;
}
