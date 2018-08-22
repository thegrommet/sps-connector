<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\CarrierInformation;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Carrier Information Element Test Suite
 */
class CarrierInformationTest extends TestCase
{
    public function testExportToXml(): void
    {
        $carrierInformation = new CarrierInformation('FDEG', 'FEDEX GROUND');
        $xml = new SimpleXMLElement('<root/>');
        $carrierInformation->exportToXml($xml);
        $this->assertSame('FDEG', (string)$xml->CarrierInformation->CarrierAlphaCode);
        $this->assertSame('FEDEX GROUND', (string)$xml->CarrierInformation->CarrierRouting);
    }

    public function testExportToXmlRequired(): void
    {
        $carrierInformation = new CarrierInformation('FDEG');
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementNotSet::class);
        $this->expectExceptionMessage('CarrierInformation: Both CarrierAlphaCode and CarrierRouting must be set.');
        $carrierInformation->exportToXml($xml);
    }
}
