<?php
declare(strict_types=1);

namespace Tests\Document;

use PHPUnit\Framework\TestCase;
use SpsConnector\Document\Shipment;

/**
 * Shipment Doc Test Suite
 */
class ShipmentTest extends TestCase
{
    public function testEdiNumber(): void
    {
        $document = new Shipment();
        $this->assertEquals(856, $document->ediNumber());
    }
}
