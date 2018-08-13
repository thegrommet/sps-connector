<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SpsConnector\Document\Element\Reference;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Reference Element Test Suite
 */
class ReferenceTest extends TestCase
{
    public function testAddToXml(): void
    {
        $reference = new Reference('LO', 'abc123');
        $xml = new SimpleXMLElement('<root/>');
        $reference->addToXml($xml);
        $this->assertEquals('LO', (string)$xml->References->ReferenceQual);
        $this->assertEquals('abc123', (string)$xml->References->ReferenceID);

        $reference = new Reference('MK', 'def');
        $reference->addToXml($xml);
        $this->assertEquals('MK', (string)$xml->References[1]->ReferenceQual);
        $this->assertEquals('def', (string)$xml->References[1]->ReferenceID);
    }

    public function testAddToXmlRequired(): void
    {
        $reference = new Reference();
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementNotSet::class);
        $this->expectExceptionMessage('Both "qualifier" and "id" must be set.');
        $reference->addToXml($xml);
    }

    public function testAddToXmlInvalidQualifier(): void
    {
        $reference = new Reference('XX', 'a');
        $xml = new SimpleXMLElement('<root/>');
        $this->expectException(ElementInvalid::class);
        $this->expectExceptionMessage('Invalid qualifier.');
        $reference->addToXml($xml);
    }
}
