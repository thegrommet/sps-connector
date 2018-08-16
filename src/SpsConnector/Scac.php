<?php
declare(strict_types=1);

namespace SpsConnector;

/**
 * SCAC manager
 */
class Scac
{
    protected $codes = [];

    public function __construct()
    {
        $this->codes = (include 'Scac/data.php');
    }

    /**
     * Returns an array of the registered codes.
     *
     * @return string[]
     */
    public function commonCodes(): array
    {
        return $this->codes;
    }

    /**
     * Lookup a carrier name by a given code.
     *
     * @param string $code
     * @return null|string
     */
    public function nameByCode(string $code): ?string
    {
        return $this->codes[$code] ?? null;
    }
}
