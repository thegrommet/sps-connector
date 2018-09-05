<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\PaymentTerms;

/**
 * PaymentTerms Element Test Suite
 */
class PaymentTermsTest extends TestCase
{
    public function testImportFromXml(): void
    {
        $terms = $this->terms();

        $this->assertSame('01', $terms->termsType);
        $this->assertSame('3', $terms->basisDateCode);
        $this->assertSame(2.0, $terms->discountPercentage);
        $this->assertSame(30, $terms->discountDueDays);
        $this->assertSame('2016-04-12', $terms->netDueDate);
        $this->assertSame(31, $terms->netDueDays);
        $this->assertSame('2% 30 Net 31', $terms->description);
        $this->assertSame(14, $terms->dueDay);
    }

    public function testFormatDescription(): void
    {
        $terms = $this->terms();
        $this->assertSame('2% 30 Net 31 terms based on Invoice Date', $terms->formatTermsDescription());

        $xml = new SimpleXMLElement('<PaymentTerms>
            <TermsType>03</TermsType>
            <TermsBasisDateCode>2</TermsBasisDateCode>
            <TermsDiscountDueDays>90</TermsDiscountDueDays>
        </PaymentTerms>');

        $terms->importFromXml($xml);
        $this->assertSame('Fixed Date terms based on Delivery Date', $terms->formatTermsDescription());
    }

    public function testCombineData(): void
    {
        $terms = $this->terms();
        $terms->dueDay = null;
        $terms->netDueDate = null;

        $this->assertSame([
            'terms_type' => '01',
            'basis_date_code' => '3',
            'discount_percentage' => 2.0,
            'discount_due_days' => 30,
            'net_due_days' => 31,
            'description' => '2% 30 Net 31',
            'terms_description' => '2% 30 Net 31 terms based on Invoice Date'
        ], $terms->combineData());
    }

    private function terms(): PaymentTerms
    {
        $xml = new SimpleXMLElement('<PaymentTerms>
            <TermsType>01</TermsType>
            <TermsBasisDateCode>3</TermsBasisDateCode>
            <TermsDiscountPercentage>2</TermsDiscountPercentage>
            <TermsDiscountDueDays>30</TermsDiscountDueDays>
            <TermsNetDueDate>2016-04-12</TermsNetDueDate>
            <TermsNetDueDays>31</TermsNetDueDays>
            <TermsDescription>2% 30 Net 31</TermsDescription>
            <TermsDueDay>14</TermsDueDay>
        </PaymentTerms>');

        $terms = new PaymentTerms();
        $terms->importFromXml($xml);
        return $terms;
    }
}
