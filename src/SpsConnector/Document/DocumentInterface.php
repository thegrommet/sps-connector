<?php
declare(strict_types=1);

namespace SpsConnector\Document;

interface DocumentInterface
{
    /**
     * EDI document type.
     *
     * @return int
     */
    public function getEdiType(): int;
}
