<?php
declare(strict_types=1);

namespace Tests\Document;

use PHPUnit\Framework\TestCase;
use SpsConnector\Document\XmlBuilderTrait;

/**
 * XmlBuilderTrait Test Suite
 */
class XmlBuilderTraitTest extends TestCase
{
    public function testToString(): void
    {
        $document = new XmlBuilderTraitImpl();
        $this->assertSame('<?xml version="1.0"?><Test/>', str_replace("\n", '', $document->__toString()));
    }

    public function testAddElement(): void
    {
        $document = new XmlBuilderTraitImpl();
        $toString = function () use ($document) {
            return str_replace("\n", '', $document->__toString());
        };
        $document->addElement('Shipment');
        $this->assertSame(
            '<?xml version="1.0"?><Test><Shipment/></Test>',
            $toString()
        );
        $document->addElement('Shipment');
        $this->assertSame(
            '<?xml version="1.0"?><Test><Shipment/><Shipment/></Test>',
            $toString()
        );
        $document->addElement('A', '1');
        $this->assertSame(
            '<?xml version="1.0"?><Test><Shipment/><Shipment/><A>1</A></Test>',
            $toString()
        );
        $document->addElement('Shipment/B');
        $this->assertSame(
            '<?xml version="1.0"?><Test><Shipment><B/></Shipment><Shipment/><A>1</A></Test>',
            $toString()
        );
        $document->addElement('Shipment/C', '2');
        $this->assertSame(
            '<?xml version="1.0"?><Test><Shipment><B/><C>2</C></Shipment><Shipment/><A>1</A></Test>',
            $toString()
        );
        $document->addElement('Shipment/D/E', '3');
        $this->assertSame(
            '<?xml version="1.0"?><Test><Shipment><B/><C>2</C><D><E>3</E></D></Shipment><Shipment/><A>1</A></Test>',
            $toString()
        );
    }

    public function testGetXmlElements(): void
    {
        $document = new XmlBuilderTraitImpl();
        $document->addElement('A/B/C');
        $document->addElement('A/B/D');
        $document->addElement('A/B');
        $a = $document->getXmlElements('A');
        $this->assertCount(1, $a);
        $b = $document->getXmlElements('A/B');
        $this->assertCount(2, $b);
        $this->assertCount(2, $b[0]->children());
    }

    public function testHasNode(): void
    {
        $document = new XmlBuilderTraitImpl();
        $document->addElement('A/B/C');
        $this->assertTrue($document->hasNode('A'));
        $this->assertTrue($document->hasNode('A/B'));
        $this->assertTrue($document->hasNode('A/B/C'));
        $this->assertFalse($document->hasNode('D'));
        $this->assertFalse($document->hasNode('D/E'));
        $this->assertFalse($document->hasNode('B'));
    }
}

class XmlBuilderTraitImpl
{
    use XmlBuilderTrait;

    public function rootElementName(): string
    {
        return 'Test';
    }
}
