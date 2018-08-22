<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\PhysicalDetails;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * PhysicalDetails Element Test Suite
 */
class PhysicalDetailsTest extends TestCase
{
    public function testExportToXml(): void
    {
        $pd = new PhysicalDetails(12.5, 4.0);
        $xml = new SimpleXMLElement('<root/>');
        $pd->exportToXml($xml);
        $this->assertSame('12.5', (string)$xml->PhysicalDetails->PackWeight);
        $this->assertSame('LB', (string)$xml->PhysicalDetails->PackWeightUOM);
        $this->assertSame('4', (string)$xml->PhysicalDetails->PackVolume);
        $this->assertSame('CI', (string)$xml->PhysicalDetails->PackVolumeUOM);
    }

    public function testExportToXmlInvalidUOM(): void
    {
        $qaw = new PhysicalDetails(12.5, 4.0, 'BAD');
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Invalid weight UOM.');
        $qaw->exportToXml($xml);
    }
}
