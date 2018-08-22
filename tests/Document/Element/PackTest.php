<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\Pack;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Pack Element Test Suite
 */
class PackTest extends TestCase
{
    public function testExportToXml(): void
    {
        $pack = new Pack('P', '7000012345');
        $xml = new SimpleXMLElement('<root/>');
        $pack->exportToXml($xml);
        $this->assertSame('P', (string)$xml->Pack->PackLevelType);
        $this->assertSame('00000000007000012345', (string)$xml->Pack->ShippingSerialID);
    }

    public function testExportToXmlRequired(): void
    {
        $pack = new Pack();
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementNotSet::class);
        $this->expectExceptionMessage('Pack: Both PackLevelType and ShippingSerialID must be set.');
        $pack->exportToXml($xml);
    }

    public function testExportToXmlInvalidQualifier(): void
    {
        $pack = new Pack('X', 'a');
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Pack: Invalid PackLevelType.');
        $pack->exportToXml($xml);
    }
}
