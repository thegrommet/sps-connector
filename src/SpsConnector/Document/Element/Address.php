<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Address element
 */
class Address implements ExportsXmlInterface, ImportsXmlInterface
{
    const TYPE_BILL_TO      = 'BT';
    const TYPE_SHIP_TO      = 'ST';
    const TYPE_SHIP_FROM    = 'SF';
    const TYPE_BUYING_PARTY = 'BY';
    const TYPE_REMIT_TO     = 'RI';

    const LOCATION_QUALIFIER_BUYER = '92';

    /**
     * The name of the exported root element.
     *
     * @var string
     */
    public $xmlRootName = 'Address';

    public $typeCode;
    public $locationQualifier;
    public $locationNumber;
    public $name;
    public $street1;
    public $street2;
    public $city;
    public $state;
    public $postalCode;
    public $country = 'USA';

    /**
     * Does this address represent a store location only (no address/city/state/etc)?
     *
     * @var bool
     */
    public $isLocationOnly = false;

    /**
     * Populate this address from the given XML.
     *
     * @param SimpleXMLElement $root
     */
    public function importFromXml(SimpleXMLElement $root): void
    {
        if ($root->getName() != 'Address') {
            throw new ElementInvalid('Address root is invalid.');
        }
        $this->typeCode = (string)$root->AddressTypeCode;
        $this->locationQualifier = (string)$root->LocationCodeQualifier;
        $this->locationNumber = (string)$root->AddressLocationNumber;
        $this->name = (string)$root->AddressName;
        $this->street1 = (string)$root->Address1;
        $this->street2 = (string)$root->Address2;
        $this->city = (string)$root->City;
        $this->state = (string)$root->State;
        $this->postalCode = (string)$root->PostalCode;
        if (isset($root->Country)) {
            $this->country = (string)$root->Country;
        }
    }

    /**
     * Adds this address's data to a new Address element which is then added to the given parent. The Address element
     * is returned.
     *
     * @param SimpleXMLElement $parent
     * @return SimpleXMLElement
     */
    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        $allowedTypes = [
            self::TYPE_SHIP_FROM,
            self::TYPE_SHIP_TO,
            self::TYPE_BUYING_PARTY,
            self::TYPE_BILL_TO,
            self::TYPE_REMIT_TO
        ];
        if ($this->xmlRootName == 'Address' && !in_array($this->typeCode, $allowedTypes)) {
            throw new ElementInvalid($this->xmlRootName . ': Invalid AddressTypeCode.');
        }
        $root = $parent->addChild($this->xmlRootName);
        // type code is not required for addresses with a different root
        $this->addChild($root, 'AddressTypeCode', $this->typeCode, $this->xmlRootName == 'Address');
        $this->addChild($root, 'LocationCodeQualifier', $this->locationQualifier, false || $this->isLocationOnly);
        $this->addChild($root, 'AddressLocationNumber', $this->locationNumber, false || $this->isLocationOnly);
        $this->addChild($root, 'AddressName', $this->name, true && !$this->isLocationOnly);
        $this->addChild($root, 'Address1', $this->street1, true && !$this->isLocationOnly);
        $this->addChild($root, 'Address2', $this->street2, false);
        $this->addChild($root, 'City', $this->city, true && !$this->isLocationOnly);
        $this->addChild($root, 'State', $this->state, true && !$this->isLocationOnly);
        $this->addChild($root, 'PostalCode', $this->postalCode, true && !$this->isLocationOnly);
        $this->addChild($root, 'Country', $this->country, false);
        return $root;
    }

    protected function addChild(SimpleXMLElement $parent, string $name, ?string $value, bool $required = true): void
    {
        if ($required && !$value) {
            throw new ElementNotSet(sprintf($this->xmlRootName . ': %s must be set.', $name));
        }
        if ($value) {
            $parent->addChild($name, $this->prepareXmlValue($value));
        }
    }

    /**
     * Format an XML value and strip it of illegal chars.
     *
     * @todo This formatting should happen for all XML element in this library - not just this class.
     * @param mixed $value
     * @return string
     */
    protected function prepareXmlValue($value): string
    {
        return htmlspecialchars(preg_replace('/[\x00-\x1F\x7F]/', '', $value));
    }
}
