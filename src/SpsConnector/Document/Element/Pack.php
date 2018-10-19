<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use Exception;
use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * Pack element
 */
class Pack implements ExportsXmlInterface
{
    const TYPE_PACKAGE = 'P';
    const TYPE_PALLET  = 'T';

    public $type;
    public $gs1CompanyPrefix;
    public $serialReference;
    public $extensionDigit;
    public $carrierPackageId;

    public function __construct(
        string $type,
        string $gs1CompanyPrefix,
        string $serialReference,
        string $extensionDigit = '0',
        string $carrierPackageId = null
    ) {
        $this->type = $type;
        $this->gs1CompanyPrefix = $gs1CompanyPrefix;
        $this->serialReference = $serialReference;
        $this->extensionDigit = $extensionDigit;
        $this->carrierPackageId = $carrierPackageId;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if ($this->type != self::TYPE_PACKAGE && $this->type != self::TYPE_PALLET) {
            throw new ElementInvalid('Pack: Invalid PackLevelType.');
        }
        if (!$this->gs1CompanyPrefix || !$this->serialReference) {
            throw new ElementInvalid('Pack: GS1 Company Prefix and Serial Reference must be set for SSCC generation.');
        }
        try {
            $sscc = self::generateSSCC($this->gs1CompanyPrefix, $this->serialReference, $this->extensionDigit);
        } catch (Exception $e) {
            throw new ElementInvalid('Pack: ' . $e->getMessage());
        }
        $root = $parent->addChild('Pack');
        $root->addChild('PackLevelType', $this->type);
        $root->addChild('ShippingSerialID', $sscc);
        if ($this->carrierPackageId) {
            $root->addChild('CarrierPackageID', $this->carrierPackageId);
        }
        return $root;
    }

    /**
     * Generate an SSCC code.
     *
     * @link https://www.gs1us.org/tools/check-digit-calculator
     * @param string $gs1CompanyPrefix
     * @param string $serialReference
     * @param string $extensionDigit
     * @return string
     */
    public static function generateSSCC(
        string $gs1CompanyPrefix,
        string $serialReference,
        string $extensionDigit = '0'
    ): string {
        $serialReference = str_pad($serialReference, 16 - strlen($gs1CompanyPrefix), '0', STR_PAD_LEFT);
        $digits = [(int)$extensionDigit];
        foreach (str_split($gs1CompanyPrefix) as $digit) {
            $digits[] = (int)$digit;
        }
        foreach (str_split($serialReference) as $digit) {
            $digits[] = (int)$digit;
        }
        if (count($digits) != 17) {
            throw new Exception(
                'Invalid GS1 Company Prefix or Serial Reference - combined, they must not exceed 16 characters.'
            );
        }
        $check = 0;
        for ($i = 0; $i < 17; $i++) {
            $check += $digits[$i] * ($i & 1 ? 1 : 3);
        }
        $check = 10 - $check % 10;
        if ($check == 10) {
            $check = 0;
        }
        return '00' . implode('', $digits) . (string)$check;
    }
}
