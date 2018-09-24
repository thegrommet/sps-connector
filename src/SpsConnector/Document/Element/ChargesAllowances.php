<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Charges Allowances element
 */
class ChargesAllowances implements ExportsXmlInterface
{
    const INDICATOR_CHARGE     = 'C';
    const INDICATOR_ALLOWANCE  = 'A';
    const CODE_DISCOUNT        = 'C310';
    const CODE_FREIGHT         = 'D240';
    const HANDLING_CODE_VENDOR = '05';
    const HANDLING_CODE_BUYER  = '06';

    public $indicator;
    public $code;
    public $amount;
    public $handlingCode;
    public $handlingDescription;

    public function __construct(
        string $indicator,
        string $code,
        float $amount,
        string $handlingCode = null,
        string $handlingDescription = null
    ) {
        $this->indicator = $indicator;
        $this->code = $code;
        $this->amount = $amount;
        $this->handlingCode = $handlingCode;
        $this->handlingDescription = $handlingDescription;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if ($this->indicator != self::INDICATOR_ALLOWANCE && $this->indicator != self::INDICATOR_CHARGE) {
            throw new ElementInvalid('ChargesAllowances: Invalid AllowChrgIndicator.');
        }
        $root = $parent->addChild('ChargesAllowances');
        $this->addChild($root, 'AllowChrgIndicator', $this->indicator);
        $this->addChild($root, 'AllowChrgCode', $this->code);
        $this->addChild($root, 'AllowChrgAmt', (string)round($this->amount, 2));
        $this->addChild($root, 'AllowChrgHandlingCode', $this->handlingCode, false);
        $this->addChild($root, 'AllowChrgHandlingDescription', $this->handlingDescription, false);

        return $root;
    }

    protected function addChild(SimpleXMLElement $parent, string $name, ?string $value, bool $required = true): void
    {
        if ($required && !$value) {
            throw new ElementNotSet(sprintf('ChargesAllowances: %s must be set.', $name));
        }
        if ($value) {
            $parent->addChild($name, $value);
        }
    }
}
