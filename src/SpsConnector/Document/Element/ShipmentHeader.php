<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Shipment Header element
 */
class ShipmentHeader implements ExportsXmlInterface
{
    use DateTimeTrait;

    const TSET_CODE_ORIGINAL = '00';
    const ASN_SOPI_CODE      = '0001';

    public $tradingPartnerId;
    public $shipmentId;
    public $shipDate;
    public $shipNoticeDate;
    public $tsetPurposeCode;
    public $asnStructureCode;
    public $billofLading;
    public $carrierPro;

    public function __construct(
        string $tradingPartnerId = null,
        string $shipmentId = null,
        string $shipDate = null,
        string $trackingNumber = null
    ) {
        $this->tradingPartnerId = $tradingPartnerId;
        $this->shipmentId = $shipmentId;
        $this->shipDate = $shipDate;
        $this->billofLading = $trackingNumber;
        $this->carrierPro = $trackingNumber;

        // defaults
        $this->tsetPurposeCode = self::TSET_CODE_ORIGINAL;
        $this->asnStructureCode = self::ASN_SOPI_CODE;
        $this->shipNoticeDate = date('Y-m-d H:i:s');
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if (!$this->shipDate) {
            throw new ElementNotSet('ShipmentHeader: Invalid ShipDate.');
        }
        if (!$this->shipNoticeDate) {
            throw new ElementNotSet('ShipmentHeader: Invalid ShipNoticeDate.');
        }
        $root = $parent->addChild('ShipmentHeader');
        $this->addChild($root, 'TradingPartnerId', $this->tradingPartnerId);
        $this->addChild($root, 'ShipmentIdentification', $this->shipmentId);
        $this->addChild($root, 'ShipDate', $this->formatDate($this->shipDate));
        $this->addChild($root, 'TsetPurposeCode', $this->tsetPurposeCode);
        $this->addChild($root, 'ShipNoticeDate', $this->formatDate($this->shipNoticeDate));
        $this->addChild($root, 'ShipNoticeTime', $this->formatTime($this->shipNoticeDate));
        $this->addChild($root, 'ASNStructureCode', $this->asnStructureCode);
        $this->addChild($root, 'BillOfLadingNumber', $this->billofLading);
        $this->addChild($root, 'CarrierProNumber', $this->carrierPro);
        return $root;
    }

    protected function addChild(SimpleXMLElement $parent, string $name, ?string $value, bool $required = true): void
    {
        if ($required && !$value) {
            throw new ElementNotSet(sprintf('ShipmentHeader: %s must be set.', $name));
        }
        if ($value) {
            $parent->addChild($name, $value);
        }
    }
}
