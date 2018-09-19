<?php
declare(strict_types=1);

namespace Tests;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use SpsConnector\LabelService\ConversionException;
use SpsConnector\LabelService\ZplConverter;

/**
 * ZPL Converter Test Suite
 */
class ZplConverterTest extends TestCase
{
    use PHPMock;
    
    private $mockNamespace = 'SpsConnector\LabelService';

    public function testToPdf(): void
    {
        $this->getFunctionMock($this->mockNamespace, 'curl_init')
            ->expects($this->once())
            ->with($this->equalTo('http://api.labelary.com/v1/printers/8dpmm/labels/4x6/0/'))
            ->willReturn(null);
        $this->getFunctionMock($this->mockNamespace, 'curl_setopt')
            ->expects($this->exactly(5))
            ->willReturn(null);
        $this->getFunctionMock($this->mockNamespace, 'curl_exec')
            ->expects($this->once())
            ->willReturn('abcde');
        $this->getFunctionMock($this->mockNamespace, 'curl_close')
            ->expects($this->once())
            ->willReturn(null);
        $this->getFunctionMock($this->mockNamespace, 'curl_getinfo')
            ->expects($this->once())
            ->willReturn(200);

        $converter = new ZplConverter();
        $this->assertSame('abcde', $converter->toPdf('abc', 8));
    }
    
    public function testToPdfError(): void
    {
        $this->getFunctionMock($this->mockNamespace, 'curl_init')
            ->expects($this->once())
            ->with($this->equalTo('http://api.labelary.com/v1/printers/8dpmm/labels/4x6/0/'))
            ->willReturn(null);
        $this->getFunctionMock($this->mockNamespace, 'curl_setopt')
            ->expects($this->exactly(5))
            ->willReturn(null);
        $this->getFunctionMock($this->mockNamespace, 'curl_exec')
            ->expects($this->once())
            ->willReturn('abcde');
        $this->getFunctionMock($this->mockNamespace, 'curl_close')
            ->expects($this->once())
            ->willReturn(null);
        $this->getFunctionMock($this->mockNamespace, 'curl_getinfo')
            ->expects($this->once())
            ->willReturn(401);
        $this->getFunctionMock($this->mockNamespace, 'curl_error')
            ->expects($this->once())
            ->willReturn('TEST MESSAGE');

        $this->expectException(ConversionException::class);
        $this->expectExceptionCode(401);
        $this->expectExceptionMessage('TEST MESSAGE');

        $converter = new ZplConverter();
        $converter->toPdf('abc');
    }
}
