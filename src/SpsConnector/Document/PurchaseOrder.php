<?php
declare(strict_types=1);

namespace SpsConnector\Document;

use SpsConnector\Document\Element\Address;
use SpsConnector\Document\Element\Contact;
use SpsConnector\Document\Element\Date;
use SpsConnector\Document\Element\LineItem;

/**
 * Purchase Order EDI document
 */
class PurchaseOrder extends IncomingDocument implements DocumentInterface
{
    const EDI_NUMBER            = 850;
    const DOCUMENT_TYPE_CODE    = 'PO';

    const TSET_ORIGINAL         = '00';
    const TSET_CANCEL           = '01';
    const TSET_REPLACE          = '05';
    const TSET_CONFIRMATION     = '06';
    const TSET_DUPLICATE        = '07';

    const ADDRESS_TYPE_BILLING  = Address::TYPE_BILL_TO;
    const ADDRESS_TYPE_SHIPPING = Address::TYPE_SHIP_TO;

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

    protected $noteCodes = [
        'CCG' => 'Customization',
        'GEN' => 'General Note',
        'GFT' => 'Gift Note',
        'MKG' => 'Marketing Message',
        'PCK' => 'Packing Slip',
        'PRN' => 'Personalization',
        'RTN' => 'Return Instructions',
        'SHP' => 'Shipping Note',
        'SPE' => 'Special Instructions'
    ];

    protected $paymentTermTypes = [
        '01' => 'Basic',
        '02' => 'End of Month',
        '03' => 'Fixed Date',
        '04' => 'Deferred or Installment',
        '05' => 'Discount Not Applicable',
        '06' => 'Mixed',
        '07' => 'Extended',
        '08' => 'Basic Discount Offered',
        '09' => 'Proximo',
        '10' => 'Instant',
        '11' => 'Elective',
        '12' => '10 Days after End of Month',
        '14' => 'Previously agreed upon',
        '18' => 'Fixed Date',
        '22' => 'Cash Discount Terms Apply',
        '23' => 'Payment Due Upon Receipt of Invoice',
        '24' => 'Anticipation',
        'CO' => 'Consignment',
        'NC' => 'No Charge',
        'PP' => 'Prepayment'
    ];

    protected $paymentTermsBasisDates = [
        '1' => 'Ship Date',
        '2' => 'Delivery Date',
        '3' => 'Invoice Date',
        '4' => 'Specified Date',
        '5' => 'Invoice Receipt Date',
        '6' => 'Anticipated Delivery Date',
        '7' => 'Effective Date',
        '8' => 'Invoice Transmission Date',
        '09' => 'Purchase Order Date',
        '15' => 'Receipt of Goods'
    ];

    protected $carrierServiceLevels = [
        '3D' => 'Three Day Service',
        'AM' => 'A.M. Service',
        'CG' => 'Ground',
        'CX' => 'Express Service',
        'DC' => 'Delivery Confirmation',
        'DS' => 'Door Service',
        'ET' => 'Proof of Delivery[POD] with Signature',
        'FC' => 'First Class',
        'G2' => 'Standard Service',
        'IDL' => 'Inside Delivery',
        'IE' => 'Expedited Service - Worldwide',
        'IS' => 'International Service',
        'IX' => 'Express Service - Worldwide',
        'LT' => 'Economy',
        'ME' => 'Metro',
        'ND' => 'Next Day Air',
        'NH' => 'Next Day Hundred Weight',
        'NXD' => 'Next Day',
        'ON' => 'Overnight',
        'PA' => 'Primary Service Area - Next Day by 10:30 A.M.',
        'PB' => 'Priority Mail',
        'PC' => 'Primary Service Area - Next Day by 9:30 A.M.',
        'PI' => 'Priority Mail Insured',
        'PM' => 'P.M. Service',
        'PO' => 'P.O. Box/Zip Code',
        'PR' => 'Primary Service Area - Next Day by 5:00 P.M.',
        'PS' => 'Primary Service Area - Second Day by Noon',
        'PX' => 'Premium Service',
        'SA' => 'Same Day',
        'SC' => 'Second Day Air',
        'SD' => 'Saturday Service',
        'SE' => 'Second Day',
        'SG' => 'Standard Ground',
        'SH' => 'Second Day Hundred Weight',
        'SI' => 'Standard Ground Hundred Weight'
    ];

    public function ediNumber(): int
    {
        return self::EDI_NUMBER;
    }

    public function documentTypeCode(): string
    {
        return self::DOCUMENT_TYPE_CODE;
    }

    public function poType(): string
    {
        return (string)$this->getXmlData('//Order/Header/OrderHeader/PrimaryPOTypeCode');
    }

    public function poTypeDescription(): string
    {
        return $this->poTypes[$this->poType()] ?? '';
    }

    public function poNumber(): string
    {
        return (string)$this->getXmlData('//Order/Header/OrderHeader/PurchaseOrderNumber');
    }

    public function tradingPartnerId(): string
    {
        return (string)$this->getXmlData('//Order/Header/OrderHeader/TradingPartnerId');
    }

    public function contactByType(string $type): ?Contact
    {
        foreach ($this->getXmlElements('//Order/Header/Contacts') as $headerContact) {
            if ((string)$headerContact->ContactTypeCode === $type) {
                $contact = new Contact();
                $contact->importFromXml($headerContact);
                return $contact;
            }
        }
        return null;
    }

    /**
     * @return Contact[]
     */
    public function contacts(): array
    {
        $contacts = [];
        foreach ($this->getXmlElements('//Order/Header/Contacts') as $headerContact) {
            $contact = new Contact();
            $contact->importFromXml($headerContact);
            $contacts[] = $contact;
        }
        return $contacts;
    }

    public function addressByType(string $type): ?Address
    {
        $addresses = $this->getXmlElements('//Order/Header/Address');
        foreach ($addresses as $headerAddress) {
            if ((string)$headerAddress->AddressTypeCode === $type) {
                $address = new Address();
                $address->importFromXml($headerAddress);
                return $address;
            }
        }
        return null;
    }

    public function combineNotes(string $separator = "\n"): string
    {
        $notes = [];
        $xmlNotes = $this->getXmlElements('//Order/Header/Notes');
        foreach ($xmlNotes as $xmlNote) {
            $notes[] = ($this->noteCodes[(string)$xmlNote->NoteCode] ?? 'N/A') . ': ' . (string)$xmlNote->Note;
        }
        return implode($separator, $notes);
    }

    public function shippingDescription(): string
    {
        $xmlCarriers = $this->getXmlElements('//Order/Header/CarrierInformation');
        if (count($xmlCarriers)) {
            $xmlCarrier = current($xmlCarriers);
            $service = (string)$xmlCarrier->ServiceLevelCodes[0]->ServiceLevelCode;
            return (string)$xmlCarrier->CarrierRouting . ' - '
                 . ($this->carrierServiceLevels[$service] ?? '[Not Specified]');
        }
        return '';
    }

    public function paymentTermsDescription(): string
    {
        $basisDate = (string)$this->getXmlData('//Order/Header/PaymentTerms/TermsBasisDateCode');
        $description = (string)$this->getXmlData('//Order/Header/PaymentTerms/TermsDescription');
        if ($description) {
            if (isset($this->paymentTermsBasisDates[$basisDate])) {
                return sprintf('%s terms based on %s', $description, $this->paymentTermsBasisDates[$basisDate]);
            }
            return $description;
        }
        $termType = (string)$this->getXmlData('//Order/Header/PaymentTerms/TermsType');
        return sprintf(
            '%s terms based on %s',
            $this->paymentTermTypes[$termType] ?? 'Unknown',
            $this->paymentTermsBasisDates[$basisDate] ?? 'Unknown'
        );
    }

    public function requestedShipDate(): ?string
    {
        $dates = $this->getXmlElements('//Order/Header/Dates');
        foreach ($dates as $date) {
            if ((string)$date->DateTimeQualifier == Date::QUALIFIER_REQUESTED_SHIP) {
                return (string)$date->Date;
            }
        }
        return null;
    }

    /**
     * @return LineItem[]
     */
    public function items(): array
    {
        $items = [];
        foreach ($this->getXmlElements('//Order/LineItem') as $xmlItem) {
            $item = new LineItem();
            $item->importFromXml($xmlItem);
            $items[] = $item;
        }
        return $items;
    }
}
