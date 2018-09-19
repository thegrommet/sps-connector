<?php
declare(strict_types=1);

namespace Tests\Document;

use PHPUnit\Framework\TestCase;
use SpsConnector\Document\ShippingLabel;

/**
 * ShippingLabel Test Suite
 */
class ShippingLabelTest extends TestCase
{
    public function testAddLabel(): void
    {
        $label = new ShippingLabel();
        $label->addLabel();
        $label->addLabel();
        $this->assertSame(
            '<?xml version="1.0"?><ShippingLabels><ShippingLabel/><ShippingLabel/></ShippingLabels>',
            str_replace("\n", '', $label->__toString())
        );
    }
}
