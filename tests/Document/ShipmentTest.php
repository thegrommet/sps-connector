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

    public function testSetHeader(): void
    {
        $document = new Shipment();
        $document->addHeader([
            'TradingPartnerId' => 'SPSALLTESTID',
            'ShipmentIdentification' => 'Sh123546-1',
            'TsetPurposeCode' => '00'
        ]);

        $expected = '<?xml version="1.0"?>' .
'<Shipments>' .
    '<Shipment>' .
        '<Header>' .
            '<ShipmentHeader>' .
                '<TradingPartnerId>SPSALLTESTID</TradingPartnerId>' .
                '<ShipmentIdentification>Sh123546-1</ShipmentIdentification>' .
                '<TsetPurposeCode>00</TsetPurposeCode>' .
            '</ShipmentHeader>' .
        '</Header>' .
    '</Shipment>' .
'</Shipments>';

        $this->assertEquals($expected, str_replace("\n", '', $document->__toString()));
    }
}
