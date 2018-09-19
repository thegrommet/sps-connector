<?php
declare(strict_types=1);

require_once '../vendor/autoload.php';

/**
 * Generate a sample label.
 */

use SpsConnector\LabelService;
use SpsConnector\LabelService\GenerationException;

try {
    $format = LabelService::FORMAT_PDF;
    $ls = new LabelService('customertest', 'spstest');
    $label = $ls->getLabel(file_get_contents('sample-label.xml'), '5311', $format);
    file_put_contents('label.' . $format, $label);
    echo "Got label.\n";
} catch (GenerationException $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    print_r($e->validationErrors);
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
