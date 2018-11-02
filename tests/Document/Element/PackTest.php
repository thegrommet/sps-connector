<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\Pack;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * Pack Element Test Suite
 */
class PackTest extends TestCase
{
    public function testExportToXml(): void
    {
        // generated SSCC
        $pack = new Pack('P');
        $pack->gs1CompanyPrefix = '00000001';
        $pack->serialReference = '00000001';
        $pack->extensionDigit = '0';
        $pack->carrierPackageId = '7123456';
        $xml = new SimpleXMLElement('<root/>');
        $pack->exportToXml($xml);
        $this->assertSame('P', (string)$xml->Pack->PackLevelType);
        $this->assertSame('00000000001000000014', (string)$xml->Pack->ShippingSerialID);
        $this->assertSame('7123456', (string)$xml->Pack->CarrierPackageID);

        // provided SSCC
        $pack = new Pack('P', '00000000001000000014');
        $xml = new SimpleXMLElement('<root/>');
        $pack->exportToXml($xml);
        $this->assertSame('P', (string)$xml->Pack->PackLevelType);
        $this->assertSame('00000000001000000014', (string)$xml->Pack->ShippingSerialID);
    }

    public function testExportToXmlInvalidQualifier(): void
    {
        $pack = new Pack('X');
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Pack: Invalid PackLevelType.');
        $pack->exportToXml($xml);
    }

    public function testExportToXmlUnsetSSCC(): void
    {
        $pack = new Pack('P');
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Pack: SSCC is not set and cannot be generated.');
        $pack->exportToXml($xml);
    }

    public function testExportToXmlInvalidSSCC(): void
    {
        $pack = new Pack('P');
        $pack->sscc = '12345';
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Pack: SSCC is invalid.');
        $pack->exportToXml($xml);
    }

    /**
     * @link https://www.barcode.graphics/check-digit-calculator/
     */
    public function testGenerateSSCC(): void
    {
        $this->assertSame('00000000001000000014', Pack::generateSSCC('00000001', '00000001'));
        $this->assertSame('00100000001000000011', Pack::generateSSCC('00000001', '00000001', '1'));
        $this->assertSame('00000000007734927436', Pack::generateSSCC('00000007', '73492743'));
        $this->assertSame('00100000007734927433', Pack::generateSSCC('00000007', '73492743', '1'));
        $this->assertSame('00005789000002318276', Pack::generateSSCC('05789000', '00231827'));
        $this->assertSame('00005789000002318276', Pack::generateSSCC('05789000', '231827'));
    }

    public function testGenerateSSCCInvalid(): void
    {
        $this->expectExceptionMessage(
            'Invalid GS1 Company Prefix or Serial Reference - combined, they must not exceed 16 characters.'
        );
        Pack::generateSSCC('000000001', '000000001');
    }

    public function testValidateSSCC(): void
    {
        $this->assertTrue(Pack::validateSSCC('00000000001000000014'));
        $this->assertTrue(Pack::validateSSCC('00100000001000000011'));
        $this->assertFalse(Pack::validateSSCC(''));
        $this->assertFalse(Pack::validateSSCC('bad'));
        $this->assertFalse(Pack::validateSSCC('123456789'));
        $this->assertFalse(Pack::validateSSCC('11100000001000000011'));
    }
}
