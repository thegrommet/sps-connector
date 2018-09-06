<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * PaymentTerms
 */
class PaymentTerms implements ImportsXmlInterface
{
    public $termsType;
    public $basisDateCode;
    public $discountPercentage;
    public $discountDueDays;
    public $netDueDate;
    public $netDueDays;
    public $description;
    public $dueDay;

    protected $termTypes = [
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

    protected $termsBasisDates = [
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

    /**
     * Populate this element from the given XML.
     *
     * @param SimpleXMLElement $root
     */
    public function importFromXml(SimpleXMLElement $root): void
    {
        if ($root->getName() != 'PaymentTerms') {
            throw new ElementInvalid('PaymentTerms root is invalid.');
        }
        $this->termsType = (string)$root->TermsType;
        $this->basisDateCode = (string)$root->TermsBasisDateCode;
        $this->discountPercentage = (float)$root->TermsDiscountPercentage;
        $this->discountDueDays = (int)$root->TermsDiscountDueDays;
        $this->netDueDate = (string)$root->TermsNetDueDate;
        $this->netDueDays = (int)$root->TermsNetDueDays;
        $this->description = (string)$root->TermsDescription;
        $this->dueDay = (int)$root->TermsDueDay;
    }

    public function formatTermsDescription(): string
    {
        if ($this->description) {
            if (isset($this->termsBasisDates[$this->basisDateCode])) {
                return sprintf(
                    '%s terms based on %s',
                    $this->description,
                    $this->termsBasisDates[$this->basisDateCode]
                );
            }
            return $this->description;
        }
        return sprintf(
            '%s terms based on %s',
            $this->termTypes[$this->termsType] ?? 'Unknown',
            $this->termsBasisDates[$this->basisDateCode] ?? 'Unknown'
        );
    }

    /**
     * Combines all non-empty data from this element into an array.
     *
     * @return array
     */
    public function combineData(): array
    {
        $data = [
            'terms_type' => $this->termsType,
            'basis_date_code' => $this->basisDateCode,
            'discount_percentage' => $this->discountPercentage,
            'discount_due_days' => $this->discountDueDays,
            'net_due_date' => $this->netDueDate,
            'net_due_days' => $this->netDueDays,
            'description' => $this->description,
            'due_day' => $this->dueDay,
            'terms_description' => $this->formatTermsDescription()
        ];
        foreach ($data as $key => $value) {
            if (empty($value)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
