<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\QuantityAndWeight;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * QuantityAndWeight Element Test Suite
 */
class QuantityAndWeightTest extends TestCase
{
    public function testExportToXml(): void
    {
        $qaw = new QuantityAndWeight(15, 22.125, 'LB');
        $xml = new SimpleXMLElement('<root/>');
        $qaw->exportToXml($xml);
        $this->assertSame('CTN', (string)$xml->QuantityAndWeight->PackingMedium);
        $this->assertSame(15, (int)$xml->QuantityAndWeight->LadingQuantity);
        $this->assertSame('22.13', (string)$xml->QuantityAndWeight->Weight);
        $this->assertSame('LB', (string)$xml->QuantityAndWeight->WeightUOM);
    }

    public function testExportToXmlInvalidUOM(): void
    {
        $qaw = new QuantityAndWeight();
        $qaw->weightUOM = 'BAD';
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('QuantityAndWeight: Invalid WeightUOM.');
        $qaw->exportToXml($xml);
    }
}
