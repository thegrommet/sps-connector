<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\ProductOrItemDescription;

/**
 * ProductOrItemDescription Element Test Suite
 */
class ProductOrItemDescriptionTest extends TestCase
{
    public function testExportToXml(): void
    {
        $item = new ProductOrItemDescription('My test product');
        $xml = new SimpleXMLElement('<root/>');
        $item->exportToXml($xml);
        $this->assertSame('My test product', (string)$xml->ProductOrItemDescription->ProductDescription);
    }

    public function testExportToXmlBadCharStrip(): void
    {
        $item = new ProductOrItemDescription("My & test* +product\r");
        $xml = new SimpleXMLElement('<address/>');
        $item->exportToXml($xml);
        $this->assertSame('My & test +product', (string)$xml->ProductOrItemDescription->ProductDescription);
    }

    public function testExportToXmlLongDescription(): void
    {
        $item = new ProductOrItemDescription(
            'F\'lint: Retractable Lint Roller Metallic Set - Case of 42 + 18 Refills With Display'
        );
        $xml = new SimpleXMLElement('<address/>');
        $item->exportToXml($xml);
        $this->assertSame(
            'F\'lint: Retractable Lint Roller Metallic Set - Case of 42 + 18 Refills With Disp',
            (string)$xml->ProductOrItemDescription->ProductDescription
        );
    }
}
