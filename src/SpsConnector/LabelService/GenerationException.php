<?php
declare(strict_types=1);

namespace SpsConnector\LabelService;

/**
 * GenerationException
 */
class GenerationException extends \Exception
{
    /**
     * SPS Error Code
     *
     * @var string
     */
    public $spsCode;

    public $validationErrors = [];
}
