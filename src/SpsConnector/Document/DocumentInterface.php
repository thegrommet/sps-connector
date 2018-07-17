<?php
declare(strict_types=1);

namespace SpsConnector\Document;

interface DocumentInterface
{
    /**
     * EDI document type.
     *
     * @return int
     */
    public function getEdiType(): int;

    /**
     * Return the value of the first matched element specified by xpath. If the element has children rather than a
     * value, an empty string is returned.
     *
     * @param string $xpath
     * @return string
     */
    public function getXmlData(string $xpath): string;

    /**
     * Return the child elements of the element specified by xpath.
     *
     * @param string $xpath
     * @return \SimpleXMLElement[]
     */
    public function getXmlElements(string $xpath): array;
}
