<?php
declare(strict_types=1);

namespace Tests\Document\Element;

use PHPUnit\Framework\TestCase;
use SpsConnector\Document\Element\DateTimeTrait;
use SpsConnector\Document\Exception\ElementInvalid;

/**
 * DateTimeTrait Test Suite
 */
class DateTimeTraitTest extends TestCase
{
    public function testFormatDate(): void
    {
        $document = new DateTimeTraitImpl();
        $this->assertSame('2018-08-12', $document->formatDate('2018-08-12'));
        $this->assertSame('2018-08-12', $document->formatDate('2018-08-12 22:54:24'));
        $this->assertSame('2018-08-12', $document->formatDate('8/12/18'));
        $this->assertSame('2018-08-01', $document->formatDate('08/01/18'));

        $this->assertSame('12-08-2018', $document->formatDate('2018-08-12', 'd-m-Y'));
        $this->assertSame('1534032000', $document->formatDate('2018-08-12', 'U'));
    }

    public function testFormatDateInvalid(): void
    {
        $document = new DateTimeTraitImpl();
        $this->expectException(ElementInvalid::class);
        $document->formatDate('bogus');
    }

    public function testFormatTime(): void
    {
        $document = new DateTimeTraitImpl();
        $this->assertSame('00:00:00+00:00', $document->formatTime('2018-08-12'));
        $this->assertSame('22:54:24+00:00', $document->formatTime('2018-08-12 22:54:24'));
        $this->assertSame('02:54:00+00:00', $document->formatTime('2:54:00'));
    }
}

class DateTimeTraitImpl
{
    use DateTimeTrait;
}
