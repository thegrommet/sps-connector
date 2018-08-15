<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * Contact element
 */
class Contact implements ElementInterface
{
    const TYPE_DELIVERY = 'RE';
    const TYPE_ORDER    = 'OC';
    const TYPE_BUYER    = 'BD';
    const TYPE_SUPPLIER = 'SU';

    public $typeCode;
    public $name;
    public $phone;
    public $email;

    public function importFromXml(SimpleXMLElement $root): void
    {
        if ($root->getName() != 'Contacts') {
            throw new ElementInvalid('Contacts root is invalid.');
        }
        $this->typeCode = (string)$root->ContactTypeCode;
        $this->name = (string)$root->ContactName;
        $this->phone = (string)$root->PrimaryPhone;
        $this->email = (string)$root->PrimaryEmail;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        $root = $parent->addChild('Contacts');
        if ($this->typeCode) {
            if ($this->typeCode != self::TYPE_DELIVERY && $this->typeCode != self::TYPE_ORDER
                && $this->typeCode != self::TYPE_BUYER && $this->typeCode != self::TYPE_SUPPLIER) {
                throw new ElementInvalid('Invalid type code.');
            }
            $root->addChild('ContactTypeCode', $this->typeCode);
        }
        $root->addChild('ContactName', $this->name);
        $root->addChild('PrimaryPhone', $this->phone);
        $root->addChild('PrimaryEmail', $this->email);
        return $root;
    }
}
