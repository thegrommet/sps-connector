<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\Date;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Date Element Test Suite
 */
class DateTest extends TestCase
{
    public function testExportToXml(): void
    {
        $date = new Date('011', '2018-08-12');
        $xml = new SimpleXMLElement('<root/>');
        $date->exportToXml($xml);
        $this->assertSame('011', (string)$xml->Dates->DateTimeQualifier);
        $this->assertSame('2018-08-12', (string)$xml->Dates->Date);

        $date = new Date('017', '9/10/18');
        $date->exportToXml($xml);
        $this->assertSame('017', (string)$xml->Dates[1]->DateTimeQualifier);
        $this->assertSame('2018-09-10', (string)$xml->Dates[1]->Date);
    }

    public function testExportToXmlRequired(): void
    {
        $date = new Date();
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementNotSet::class);
        $this->expectExceptionMessage('Dates: Both DateTimeQualifier and Date must be set.');
        $date->exportToXml($xml);
    }

    public function testExportToXmlInvalidQualifier(): void
    {
        $date = new Date('055', 'a');
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Dates: Invalid DateTimeQualifier.');
        $date->exportToXml($xml);
    }

    public function testExportToXmlInvalidDate(): void
    {
        $date = new Date('011', 'bogus');
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Invalid date.');
        $date->exportToXml($xml);
    }

    public function testImportFromXml(): void
    {
        $xml = new SimpleXMLElement('<Dates>
            <DateTimeQualifier>010</DateTimeQualifier>
            <Date>2018-08-25</Date>
        </Dates>');

        $date = new Date();
        $date->importFromXml($xml);
        $this->assertSame('010', $date->qualifier);
        $this->assertSame('2018-08-25', $date->date);
    }

    public function testQualifierLabel(): void
    {
        $date = new Date();
        $this->assertSame('Cancel Date', $date->qualifierLabel('001'));
        $this->assertSame('Requested Ship', $date->qualifierLabel('010'));
        $this->assertSame('', $date->qualifierLabel('BOGUS'));
    }

    public function testTimestamp(): void
    {
        $date = new Date('010', '2018-08-12');
        $this->assertSame(1534032000, $date->timestamp());
        $date->date = '2018-08-12 00:00:55';
        $this->assertSame(1534032055, $date->timestamp());
    }
}
