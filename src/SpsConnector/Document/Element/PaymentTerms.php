<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * PaymentTerms
 */
class PaymentTerms implements ExportsXmlInterface, ImportsXmlInterface
{
    use DateTimeTrait;

    public $termsType;
    public $basisDateCode;
    public $discountPercentage;
    public $discountAmount;
    public $discountDate;
    public $discountDueDays;
    public $netDueDate;
    public $netDueDays;
    public $description;
    public $dueDay;

    protected $termsTypes = [
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
        $this->discountAmount = (float)$root->TermsDiscountAmount;
        $this->discountDate = (string)$root->TermsDiscountDate;
        $this->discountDueDays = (int)$root->TermsDiscountDueDays;
        $this->netDueDate = (string)$root->TermsNetDueDate;
        $this->netDueDays = (int)$root->TermsNetDueDays;
        $this->description = (string)$root->TermsDescription;
        $this->dueDay = (int)$root->TermsDueDay;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if (!array_key_exists($this->termsType, $this->termsTypes)) {
            throw new ElementInvalid('PaymentTerms: Invalid TermsType.');
        }
        $root = $parent->addChild('PaymentTerms');
        $this->addChild($root, 'TermsType', $this->termsType);
        $this->addChild($root, 'TermsBasisDateCode', $this->basisDateCode);
        $this->addChild($root, 'TermsDiscountPercentage', (string)$this->discountPercentage, false);
        $this->addChild($root, 'TermsDiscountAmount', (string)$this->discountAmount, false);
        $this->addChild($root, 'TermsDiscountDate', $this->formatDate((string)$this->discountDate));
        $this->addChild($root, 'TermsDiscountDueDays', (string)$this->discountDueDays, false);
        $this->addChild($root, 'TermsNetDueDate', $this->formatDate((string)$this->netDueDate));
        $this->addChild($root, 'TermsNetDueDays', (string)$this->netDueDays, false);
        $this->addChild($root, 'TermsDescription', $this->description, false);
        $this->addChild($root, 'TermsDueDay', (string)$this->dueDay, false);
        return $root;
    }

    protected function addChild(SimpleXMLElement $parent, string $name, ?string $value, bool $required = true): void
    {
        if ($required && !$value) {
            throw new ElementNotSet(sprintf('PaymentTerms: %s must be set.', $name));
        }
        if ($value) {
            $parent->addChild($name, $value);
        }
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
            $this->termsTypes[$this->termsType] ?? 'Unknown',
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
            'discount_amount' => $this->discountAmount,
            'discount_date' => $this->discountDate,
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
