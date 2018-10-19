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
        $pack = new Pack('P', '00000001', '00000001', '0', '7123456');
        $xml = new SimpleXMLElement('<root/>');
        $pack->exportToXml($xml);
        $this->assertSame('P', (string)$xml->Pack->PackLevelType);
        $this->assertSame('00000000001000000014', (string)$xml->Pack->ShippingSerialID);
        $this->assertSame('7123456', (string)$xml->Pack->CarrierPackageID);
    }

    public function testExportToXmlInvalidQualifier(): void
    {
        $pack = new Pack('X', '0', '0');
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Pack: Invalid PackLevelType.');
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
}
