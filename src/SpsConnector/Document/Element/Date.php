<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Date element
 */
class Date implements ExportsXmlInterface
{
    use DateTimeTrait;

    const QUALIFIER_REQUESTED_SHIP = '010';
    const QUALIFIER_SHIP           = '011';
    const QUALIFIER_EST_DELIVERY   = '017';

    public $qualifier;
    public $date;

    public function __construct(string $qualifier = null, string $date = null)
    {
        $this->qualifier = $qualifier;
        $this->date = $date;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if (!$this->qualifier || !$this->date) {
            throw new ElementNotSet('Both "qualifier" and "date" must be set.');
        }
        if ($this->qualifier != self::QUALIFIER_REQUESTED_SHIP && $this->qualifier != self::QUALIFIER_EST_DELIVERY
            && $this->qualifier != self::QUALIFIER_SHIP) {
            throw new ElementInvalid('Invalid qualifier.');
        }
        $root = $parent->addChild('Dates');
        $root->addChild('DateTimeQualifier', $this->qualifier);
        $root->addChild('Date', $this->formatDate($this->date));
        return $root;
    }
}
