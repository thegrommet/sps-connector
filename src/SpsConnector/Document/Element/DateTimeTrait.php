<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SpsConnector\Document\AbstractDocument;
use SpsConnector\Document\Exception\ElementInvalid;
use TypeError;

/**
 * Trait for formatting dates and times.
 */
trait DateTimeTrait
{
    /**
     * Convert a date-time string to an SPS-compatible date.
     *
     * @param string $date
     * @return string
     * @throws ElementInvalid
     */
    public function formatDate(string $date): string
    {
        try {
            return date(AbstractDocument::DATE_FORMAT, strtotime($date));
        } catch (TypeError $e) {
            throw new ElementInvalid('Invalid date.');
        }
    }

    /**
     * Convert a date-time string to an SPS-compatible time.
     *
     * @param string $date
     * @return string
     * @throws ElementInvalid
     */
    public function formatTime(string $date): string
    {
        try {
            return date(AbstractDocument::TIME_FORMAT, strtotime($date));
        } catch (TypeError $e) {
            throw new ElementInvalid('Invalid time.');
        }
    }
}
