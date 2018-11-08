<?php

namespace SpsConnector\Document\Element;

/**
 * Abstract XML Element
 */
abstract class AbstractElement
{
    const EDI_ALLOW_REGEX = '/[^a-z0-9 \-_:\.,!\'"&#\$]/i';

    /**
     * Removes characters not allowed in EDI transmission.
     *
     * @param string $value
     * @return string
     */
    public function filterForEdi(string $value): string
    {
        return preg_replace(self::EDI_ALLOW_REGEX, '', $value);
    }
}
