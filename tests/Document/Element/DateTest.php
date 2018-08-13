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
    public function testAddToXml(): void
    {
        $date = new Date('011', '2018-08-12');
        $xml = new SimpleXMLElement('<root/>');
        $date->addToXml($xml);
        $this->assertEquals('011', (string)$xml->Dates->DateTimeQualifier);
        $this->assertEquals('2018-08-12', (string)$xml->Dates->Date);

        $date = new Date('017', '9/10/18');
        $date->addToXml($xml);
        $this->assertEquals('017', (string)$xml->Dates[1]->DateTimeQualifier);
        $this->assertEquals('2018-09-10', (string)$xml->Dates[1]->Date);
    }

    public function testAddToXmlRequired(): void
    {
        $date = new Date();
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementNotSet::class);
        $this->expectExceptionMessage('Both "qualifier" and "date" must be set.');
        $date->addToXml($xml);
    }

    public function testAddToXmlInvalidQualifier(): void
    {
        $date = new Date('055', 'a');
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Invalid qualifier.');
        $date->addToXml($xml);
    }

    public function testAddToXmlInvalidDate(): void
    {
        $date = new Date('011', 'bogus');
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Invalid date.');
        $date->addToXml($xml);
    }
}
