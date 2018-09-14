<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\PaymentTerms;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

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
        $this->assertSame(24.5, $terms->discountAmount);
        $this->assertSame('2016-04-10', $terms->discountDate);
        $this->assertSame(30, $terms->discountDueDays);
        $this->assertSame('2016-04-12', $terms->netDueDate);
        $this->assertSame(31, $terms->netDueDays);
        $this->assertSame('2% 30 Net 31', $terms->description);
        $this->assertSame(14, $terms->dueDay);
    }
    
    public function testExportToXml(): void
    {
        $terms = $this->terms();
        $xml = new SimpleXMLElement('<terms/>');
        $terms->exportToXml($xml);
        $this->assertSame('01', (string)$xml->PaymentTerms->TermsType);
        $this->assertSame('3', (string)$xml->PaymentTerms->TermsBasisDateCode);
        $this->assertSame(2.0, (float)$xml->PaymentTerms->TermsDiscountPercentage);
        $this->assertSame(24.5, (float)$xml->PaymentTerms->TermsDiscountAmount);
        $this->assertSame('2016-04-10', (string)$xml->PaymentTerms->TermsDiscountDate);
        $this->assertSame(30, (int)$xml->PaymentTerms->TermsDiscountDueDays);
        $this->assertSame('2016-04-12', (string)$xml->PaymentTerms->TermsNetDueDate);
        $this->assertSame(31, (int)$xml->PaymentTerms->TermsNetDueDays);
        $this->assertSame('2% 30 Net 31', (string)$xml->PaymentTerms->TermsDescription);
    }

    public function testExportToXmlOmitted(): void
    {
        $terms = $this->terms();
        $terms->discountPercentage = '';
        $xml = new SimpleXMLElement('<terms/>');
        $terms->exportToXml($xml);
        $this->assertFalse(isset($xml->PaymentTerms->TermsDiscountPercentage));
    }

    public function testExportToXmlRequired(): void
    {
        $terms = $this->terms();
        $terms->basisDateCode = '';
        $xml = new SimpleXMLElement('<address/>');
        $this->expectException(ElementNotSet::class);
        $this->expectExceptionMessage('PaymentTerms: TermsBasisDateCode must be set.');
        $terms->exportToXml($xml);
    }

    public function testExportToXmlInvalidType(): void
    {
        $terms = $this->terms();
        $terms->termsType = 'BAD';
        $xml = new SimpleXMLElement('<address/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('PaymentTerms: Invalid TermsType.');
        $terms->exportToXml($xml);
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
            'discount_amount' => 24.5,
            'discount_date' => '2016-04-10',
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
            <TermsDiscountAmount>24.5</TermsDiscountAmount>
            <TermsDiscountDate>2016-04-10</TermsDiscountDate>
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
