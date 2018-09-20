<?php
declare(strict_types=1);

namespace SpsConnector;

use Psr\Log\LoggerInterface;
use SoapClient;
use SoapFault;
use SpsConnector\LabelService\GenerationException;
use SpsConnector\LabelService\ZplConverter;

/**
 * Label Service
 */
class LabelService
{
    const FORMAT_ZPL            = 'zpl';
    const FORMAT_PDF            = 'pdf';
    const RESPONSE_CODE_SUCCESS = 'LabelService_01';

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $wsdl = 'https://labelservice.hosted-commerce.net/ls/SPSLabelServiceSoapHttpPort?wsdl';

    /**
     * @var SoapClient
     */
    protected $soapClient;

    /**
     * @var array
     */
    protected $soapOptions = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        string $username,
        string $password,
        LoggerInterface $logger = null,
        string $wsdlUrl = null
    ) {
        $this->username = $username;
        $this->password = $password;
        if ($logger) {
            $this->setLogger($logger);
        }
        if (!empty($wsdlUrl)) {
            $this->wsdl = $wsdlUrl;
        }
    }

    public function getLabel(string $labelXml, string $labelUID, string $format = self::FORMAT_PDF): string
    {
        function requestToString(SoapClient $client, string $xml)
        {
            return print_r([
                'xml' => $xml,
                'request' => preg_replace('/password>.+</', 'password>***<', $client->__getLastRequest()),
                'response' => $client->__getLastResponse(),
            ], true);
        };
        try {
            $response = $this->soapClient()->getLabel([
                'username' => $this->username,
                'password' => $this->password,
                'labelUID' => $labelUID,
                'labelData' => $labelXml
            ]);
            if ($this->logger) {
                $this->logger->info(requestToString($this->soapClient(), $labelXml));
            }
        } catch (SoapFault $e) {
            if ($this->logger) {
                $this->logger->error(requestToString($this->soapClient(), $labelXml));
            }
            throw new GenerationException($e->getMessage(), 0, $e);
        }
        if (!$response || !($response instanceof \stdClass) || !isset($response->result)) {
            throw new GenerationException('Response is empty');
        }
        $response = $response->result;
        if ($response->statusCode != self::RESPONSE_CODE_SUCCESS) {
            $e = new GenerationException($response->statusMessage);
            $e->spsCode = $response->statusCode;
            if (is_string($response->validationErrors->item)) {
                $e->validationErrors = [$response->validationErrors->item];
            } elseif (is_array($response->validationErrors->item)) {
                $e->validationErrors = $response->validationErrors->item;
            }
            throw $e;
        }
        if (empty($response->payload)) {
            throw new GenerationException('Label is missing from the response');
        }
        $label = base64_decode($response->payload);
        switch ($format) {
            case self::FORMAT_PDF:
                $converter = new ZplConverter();
                return $converter->toPdf($label);
            case self::FORMAT_ZPL:
            default:
                return $label;
        }
    }

    public function soapClient(): SoapClient
    {
        if ($this->soapClient === null) {
            $this->soapClient = new SoapClient($this->wsdl, $this->soapOptions);
        }
        return $this->soapClient;
    }

    public function setSoapClient(SoapClient $client): self
    {
        $this->soapClient = $client;
        return $this;
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;
        $this->soapOptions['trace'] = true;
        return $this;
    }
}
