<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Invoice Header element
 */
class InvoiceHeader implements ExportsXmlInterface
{
    use DateTimeTrait;

    const TYPE_DEBIT = 'DR';
    const TYPE_FINAL = 'FB';

    public $tradingPartnerId;
    public $invoiceNumber;
    public $invoiceDate;
    public $purchaseOrderDate;
    public $purchaseOrderNumber;
    public $primaryPOTypeCode;
    public $invoiceTypeCode;
    public $buyersCurrency;
    public $department;
    public $vendor;
    public $billOfLading;
    public $carrierPro;
    public $shipDate;

    protected $poTypes = [
        '26' => 'Replace',
        'BK' => 'Blanket Order',
        'CF' => 'Confirmation',
        'CN' => 'Consigned Order',
        'DS' => 'Drop Ship',
        'EO' => 'Emergency Order',
        'IN' => 'Information Copy',
        'KC' => 'Contract',
        'KN' => 'Cross Dock',
        'NS' => 'New Store Order',
        'NE' => 'New Store Order',
        'OS' => 'Special Order',
        'PR' => 'Promotion Information',
        'RE' => 'Reorder',
        'RL' => 'Release or Delivery Order',
        'RO' => 'Rush Order',
        'SA' => 'Stand Alone',
        'SD' => 'Direct to Store',
        'SP' => 'Sample Order',
        'SS' => 'Supply or Service Order',
        'WH' => 'Warehouse',
    ];

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if (!array_key_exists($this->primaryPOTypeCode, $this->poTypes)) {
            throw new ElementInvalid('InvoiceHeader: Invalid PrimaryPOTypeCode.');
        }
        if ($this->invoiceTypeCode != self::TYPE_DEBIT && $this->invoiceTypeCode != self::TYPE_FINAL) {
            throw new ElementInvalid('InvoiceHeader: Invalid InvoiceTypeCode.');
        }
        $root = $parent->addChild('InvoiceHeader');
        $this->addChild($root, 'TradingPartnerId', $this->tradingPartnerId);
        $this->addChild($root, 'InvoiceNumber', $this->invoiceNumber);
        $this->addChild($root, 'InvoiceDate', $this->formatDate((string)$this->invoiceDate));
        $this->addChild($root, 'PurchaseOrderDate', $this->formatDate((string)$this->purchaseOrderDate));
        $this->addChild($root, 'PurchaseOrderNumber', $this->purchaseOrderNumber);
        $this->addChild($root, 'PrimaryPOTypeCode', $this->primaryPOTypeCode);
        $this->addChild($root, 'InvoiceTypeCode', $this->invoiceTypeCode);
        $this->addChild($root, 'BuyersCurrency', $this->buyersCurrency, false);
        $this->addChild($root, 'Department', $this->department, false);
        $this->addChild($root, 'Vendor', $this->vendor);
        $this->addChild($root, 'BillOfLadingNumber', $this->billOfLading, false);
        $this->addChild($root, 'CarrierProNumber', $this->carrierPro, false);
        $this->addChild($root, 'ShipDate', $this->formatDate((string)$this->shipDate));
        return $root;
    }

    protected function addChild(SimpleXMLElement $parent, string $name, ?string $value, bool $required = true): void
    {
        if ($required && !$value) {
            throw new ElementNotSet(sprintf('InvoiceHeader: %s must be set.', $name));
        }
        if ($value) {
            $parent->addChild($name, $value);
        }
    }
}
