<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * ProductOrItemDescription element
 */
class ProductOrItemDescription extends AbstractElement implements ExportsXmlInterface
{
    const DESCRIPTION_MAX_LENGTH = 80;

    public $description;

    public function __construct(string $description)
    {
        $this->description = $description;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if (!$this->description) {
            throw new ElementNotSet('ProductOrItemDescription: ProductDescription must be set.');
        }
        $root = $parent->addChild('ProductOrItemDescription');
        $root->addChild('ProductDescription', $this->prepareXmlValue((string)$this->description));
        return $root;
    }

    /**
     * Format an XML value and strip it of illegal chars.
     *
     * @param string $value
     * @return string
     */
    protected function prepareXmlValue(string $value): string
    {
        return htmlspecialchars(
            substr($this->filterForEdi($value), 0, self::DESCRIPTION_MAX_LENGTH)
        );
    }
}
