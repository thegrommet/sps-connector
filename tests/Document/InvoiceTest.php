<?php
declare(strict_types=1);

namespace Tests\Document;

use PHPUnit\Framework\TestCase;
use SpsConnector\Document\Element\Address;
use SpsConnector\Document\Invoice;

/**
 * Invoice Doc Test Suite
 */
class InvoiceTest extends TestCase
{
    public function testEdiNumber(): void
    {
        $document = new Invoice();
        $this->assertSame(810, $document->ediNumber());
    }

    public function testAddHeaderAddress(): void
    {
        $document = new Invoice();
        $address = $document->addHeaderAddress($this->address());
        $this->assertSame('Main Warehouse', (string)$address->AddressName);
    }

    private function address(): Address
    {
        $address = new Address();
        $address->typeCode = Address::TYPE_SHIP_FROM;
        $address->locationNumber = '012';
        $address->name = 'Main Warehouse';
        $address->street1 = '123 Main';
        $address->street2 = 'Suite B';
        $address->city = 'Boulder';
        $address->state = 'CO';
        $address->postalCode = '80301';

        return $address;
    }
}
