<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;
use TypeError;

/**
 * Date element
 */
class Date implements ElementInterface
{
    const QUALIFIER_SHIP         = '011';
    const QUALIFIER_EST_DELIVERY = '017';

    public $qualifier;
    public $date;

    public function __construct(string $qualifier = null, string $date = null)
    {
        $this->qualifier = $qualifier;
        $this->date = $date;
    }

    public function addToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if (!$this->qualifier || !$this->date) {
            throw new ElementNotSet('Both "qualifier" and "date" must be set.');
        }
        if ($this->qualifier != self::QUALIFIER_EST_DELIVERY && $this->qualifier != self::QUALIFIER_SHIP) {
            throw new ElementInvalid('Invalid qualifier.');
        }
        try {
            $date = date('Y-m-d', strtotime($this->date));
        } catch (TypeError $e) {
            $date = false;
        }
        if ($date === false) {
            throw new ElementInvalid('Invalid date.');
        }
        $dateRoot = $parent->addChild('Dates');
        $dateRoot->addChild('DateTimeQualifier', $this->qualifier);
        $dateRoot->addChild('Date', $date);
        return $dateRoot;
    }
}
