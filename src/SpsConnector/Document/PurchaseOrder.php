<?php
declare(strict_types=1);

namespace SpsConnector\Document;

use SimpleXMLElement;

/**
 * Purchase Order EDI document
 */
class PurchaseOrder extends IncomingDocument implements DocumentInterface
{
    const EDI_NUMBER                    = 850;
    const DOCUMENT_TYPE_CODE            = 'PO';

    const TSET_ORIGINAL                 = '00';
    const TSET_CANCEL                   = '01';
    const TSET_REPLACE                  = '05';
    const CONTACT_TYPE_PRIMARY          = 'IC';
    const ADDRESS_TYPE_BILLING          = 'BT';
    const ADDRESS_TYPE_SHIPPING         = 'ST';
    const DATE_QUALIFIER_REQUESTED_SHIP = '010';

    protected $contactTypes = [
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

    public function poNumber(): string
    {
        return (string)$this->getXmlData('//Order/Header/OrderHeader/PurchaseOrderNumber');
    }

    public function contactByType(string $type): ?SimpleXMLElement
    {
        $contacts = $this->getXmlElements('//Order/Header/Contacts');
        foreach ($contacts as $contact) {
            if ((string)$contact->ContactTypeCode === $type) {
                return $contact;
            }
        }
        return null;
    }

    public function addressByType(string $type): ?SimpleXMLElement
    {
        $addresses = $this->getXmlElements('//Order/Header/Address');
        foreach ($addresses as $address) {
            if ((string)$address->AddressTypeCode === $type) {
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
            $notes[] = ($this->contactTypes[(string)$xmlNote->NoteCode] ?? 'N/A') . ': ' . (string)$xmlNote->Note;
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
            if ((string)$date->DateTimeQualifier == self::DATE_QUALIFIER_REQUESTED_SHIP) {
                return (string)$date->Date;
            }
        }
        return null;
    }

    public function newItem(): PurchaseOrderItem
    {
        return new PurchaseOrderItem();
    }
}
