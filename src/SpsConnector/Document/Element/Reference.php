<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Reference element
 */
class Reference implements ElementInterface
{
    const QUALIFIER_LOAD_PLANNING = 'LO';
    const QUALIFIER_MANIFEST      = 'MK';
    const QUALIFIER_WAREHOUSE_LOC = 'WS';

    public $qualifier;
    public $id;

    public function __construct(string $qualifier = null, string $id = null)
    {
        $this->qualifier = $qualifier;
        $this->id = $id;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if (!$this->qualifier || !$this->id) {
            throw new ElementNotSet('Both "qualifier" and "id" must be set.');
        }
        if ($this->qualifier != self::QUALIFIER_LOAD_PLANNING && $this->qualifier != self::QUALIFIER_MANIFEST
            && $this->qualifier != self::QUALIFIER_WAREHOUSE_LOC) {
            throw new ElementInvalid('Invalid qualifier.');
        }
        $root = $parent->addChild('References');
        $root->addChild('ReferenceQual', $this->qualifier);
        $root->addChild('ReferenceID', $this->id);
        return $root;
    }
}
