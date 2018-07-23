<?php
declare(strict_types=1);

namespace Tests\Document;

use PHPUnit\Framework\TestCase;
use SpsConnector\Document\PurchaseOrderItem;

/**
 * Purchase Order Item Test Suite
 */
class PurchaseOrderItemTest extends TestCase
{
    public function testPrice(): void
    {
        $this->assertEquals(20, $this->document()->price());
    }

    public function testQty(): void
    {
        $this->assertEquals(5, $this->document()->qty());
    }

    public function testRowTotal(): void
    {
        $this->assertEquals(100, $this->document()->rowTotal());
    }

    public function testComparePricingUOM(): void
    {
        $this->assertTrue($this->document()->comparePricingUOM());
    }

    private function document(): PurchaseOrderItem
    {
        $document = new PurchaseOrderItem();
        $document->setXml(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'PurchaseOrderItemTest.xml'));
        return $document;
    }
}
