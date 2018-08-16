<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;

/**
 * Exports XML Interface
 */
interface ExportsXmlInterface
{
    /**
     * Add this element's information to the given XML parent.
     *
     * @param SimpleXMLElement $parent
     * @return SimpleXMLElement The added element under $parent
     */
    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement;
}
