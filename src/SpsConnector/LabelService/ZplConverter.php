<?php
declare(strict_types=1);

namespace SpsConnector\LabelService;

/**
 * ZPL Converter
 */
class ZplConverter
{
    protected $options = [];

    /**
     * Convert the ZPL string to a PDF.
     *
     * @link http://labelary.com/service.html#php
     * @param string $zpl
     * @param int $dpmm
     * @param string $dimensions {width}x{height} format
     * @return string
     */
    public function toPdf(string $zpl, int $dpmm = 8, string $dimensions = '4x6'): string
    {
        $url = sprintf(
            'http://api.labelary.com/v1/printers/%ddpmm/labels/%s/0/',
            $dpmm,
            $dimensions
        );
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $zpl);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept: application/pdf']);

        $result = curl_exec($curl);
        $statusCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode == 200) {
            curl_close($curl);
            return $result;
        } else {
            $e = new ConversionException(curl_error($curl), $statusCode);
            curl_close($curl);
            throw $e;
        }
    }
}
