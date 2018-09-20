<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SoapClient;
use SoapFault;
use SpsConnector\LabelService;
use SpsConnector\LabelService\GenerationException;

/**
 * Label Service Test Suite
 */
class LabelServiceTest extends TestCase
{
    public function testGetLabel(): void
    {
        $response = new \stdClass();
        $response->result = new \stdClass();
        $response->result->statusCode = 'LabelService_01';
        $response->result->statusMessage = 'Payload contains label data';
        $response->result->validationErrors = [];
        $response->result->payload = 'bGFiZWwgY29udGVudHMuLi4';

        $soap = $this->soapClient();
        $soap->expects($this->once())
            ->method('getLabel')
            ->willReturn($response);

        $labelService = new LabelService('uname', 'secret');
        $labelService->setSoapClient($soap);
        $this->assertSame('label contents...', $labelService->getLabel('<xml />', '1234', $labelService::FORMAT_ZPL));
    }

    public function testGetLabelFault(): void
    {
        $soap = $this->soapClient();
        $soap->expects($this->once())
            ->method('getLabel')
            ->willThrowException(new SoapFault('5', 'Something bad'));

        $labelService = new LabelService('uname', 'secret');
        $labelService->setSoapClient($soap);

        $this->expectException(GenerationException::class);
        $this->expectExceptionMessage('Something bad');
        $labelService->getLabel('<xml />', '1234', $labelService::FORMAT_ZPL);
    }

    public function testGetLabelEmptyResponse(): void
    {
        $soap = $this->soapClient();
        $soap->expects($this->once())
            ->method('getLabel')
            ->willReturn(null);

        $labelService = new LabelService('uname', 'secret');
        $labelService->setSoapClient($soap);

        $this->expectException(GenerationException::class);
        $this->expectExceptionMessage('Response is empty');
        $labelService->getLabel('<xml />', '1234', $labelService::FORMAT_ZPL);
    }

    public function testGetLabelError(): void
    {
        $response = new \stdClass();
        $response->result = new \stdClass();
        $response->result->statusCode = 'LabelService_02';
        $response->result->statusMessage = 'Authentication error';
        $response->result->validationErrors = [];
        $response->result->payload = '';

        $soap = $this->soapClient();
        $soap->expects($this->once())
            ->method('getLabel')
            ->willReturn($response);

        $labelService = new LabelService('uname', 'secret');
        $labelService->setSoapClient($soap);

        $this->expectException(GenerationException::class);
        $this->expectExceptionMessage('Authentication error');
        $labelService->getLabel('<xml />', '1234', $labelService::FORMAT_ZPL);
    }

    public function testGetLabelValidationError(): void
    {
        $errors = [
            'Error 1',
            'Error text 2'
        ];
        $response = new \stdClass();
        $response->result = new \stdClass();
        $response->result->statusCode = 'SimpleLabelValidator_01';
        $response->result->statusMessage = 'Label request failed validation check.';
        $response->result->validationErrors = new \stdClass();
        $response->result->validationErrors->item = $errors;
        $response->result->payload = '';

        $soap = $this->soapClient();
        $soap->expects($this->exactly(2))
            ->method('getLabel')
            ->willReturn($response);

        $labelService = new LabelService('uname', 'secret');
        $labelService->setSoapClient($soap);

        // multiple errors
        $ex = null;
        try {
            $labelService->getLabel('<xml />', '1234', $labelService::FORMAT_ZPL);
        } catch (GenerationException $e) {
            $ex = $e;
        }
        $this->assertInstanceOf(GenerationException::class, $ex);
        $this->assertSame($errors, $ex->validationErrors);

        // single error
        $response->result->validationErrors->item = 'Error 1';
        $ex = null;
        try {
            $labelService->getLabel('<xml />', '1234', $labelService::FORMAT_ZPL);
        } catch (GenerationException $e) {
            $ex = $e;
        }
        $this->assertInstanceOf(GenerationException::class, $ex);
        $this->assertSame(['Error 1'], $ex->validationErrors);
    }

    private function soapClient(): MockObject
    {
        $soap = $this->getMockBuilder(SoapClient::class)
            ->setMethods(['__construct', 'getLabel'])
            ->disableOriginalConstructor()
            ->getMock();

        return $soap;
    }
}
