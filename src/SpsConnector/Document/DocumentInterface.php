<?php
declare(strict_types=1);

namespace SpsConnector\Document;

interface DocumentInterface
{
    /**
     * Returns the EDI document number.
     *
     * @return int
     */
    public function ediNumber(): int;

    /**
     * Returns the 2-character document type code.
     *
     * @return string
     */
    public function documentTypeCode(): string;
}
