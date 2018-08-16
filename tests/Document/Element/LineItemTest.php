<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\LineItem;

/**
 * LineItem Element Test Suite
 */
class LineItemTest extends TestCase
{
    public function testImportFromXml(): void
    {
        $xml = new SimpleXMLElement('<LineItem>
            <OrderLine>
                <LineSequenceNumber>1</LineSequenceNumber>
                <BuyerPartNumber>9707209</BuyerPartNumber>
                <VendorPartNumber>1691-H-103</VendorPartNumber>
                <GTIN>00854410004963</GTIN>
                <OrderQty>3</OrderQty>
                <OrderQtyUOM>EA</OrderQtyUOM>
                <PurchasePrice>3.83</PurchasePrice>
                <PurchasePriceBasis>PE</PurchasePriceBasis>
                <ProductID>
                    <PartNumberQual>MG</PartNumberQual>
                    <PartNumber>9565452</PartNumber>
                </ProductID>
            </OrderLine>
            <ProductOrItemDescription>
                <ProductCharacteristicCode>08</ProductCharacteristicCode>
                <ProductDescription>GRMT SOAP BBNOAK BRL10OZ</ProductDescription>
            </ProductOrItemDescription>
        </LineItem>');

        $line = new LineItem();
        $line->importFromXml($xml);

        $this->assertSame(1, $line->sequenceNumber);
        $this->assertSame('9707209', $line->buyerPartNumber);
        $this->assertSame('1691-H-103', $line->vendorPartNumber);
        $this->assertSame('00854410004963', $line->gtin);
        $this->assertSame(3, $line->orderedQty);
        $this->assertSame('EA', $line->orderedQtyUOM);
        $this->assertSame(3.83, $line->purchasePrice);
        $this->assertSame('PE', $line->purchasePriceBasis);
        $this->assertSame('9565452', $line->productPartNumber);
        $this->assertSame('08', $line->productCode);
        $this->assertSame('GRMT SOAP BBNOAK BRL10OZ', $line->productDescription);
    }
}
