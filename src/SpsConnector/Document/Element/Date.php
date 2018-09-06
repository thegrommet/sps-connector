<?php
declare(strict_types=1);

namespace SpsConnector\Document\Element;

use SimpleXMLElement;
use SpsConnector\Document\Exception\ElementInvalid;
use SpsConnector\Document\Exception\ElementNotSet;

/**
 * Date element
 */
class Date implements ExportsXmlInterface, ImportsXmlInterface
{
    use DateTimeTrait;

    const QUALIFIER_REQUESTED_SHIP = '010';
    const QUALIFIER_SHIP           = '011';
    const QUALIFIER_EST_DELIVERY   = '017';

    public $qualifier;
    public $date;

    protected $qualifiers = [
        '001' => 'Cancel Date',
        '002' => 'Requested Delivery',
        '006' => 'Customer Order',
        '007' => 'Effective',
        '010' => 'Requested Ship',
        '011' => 'Actual Ship',
        '012' => 'Discount Due',
        '013' => 'Net Due Date',
        '015' => 'Promotion Start',
        '016' => 'Promotion End',
        '017' => 'Estimated Delivery',
        '018' => 'Available',
        '019' => 'Date Unloaded',
        '020' => 'Check',
        '035' => 'Actual Delivery',
        '036' => 'Discontinued',
        '037' => 'Earliest Ship',
        '038' => 'Latest Ship',
        '043' => 'Published/Publication',
        '050' => 'Received Date',
        '057' => 'Actual Port of Entry',
        '060' => 'Engineering Change',
        '063' => 'Latest Delivery',
        '064' => 'Earliest Delivery',
        '067' => 'Current Schedule Delivery',
        '068' => 'Scheduled Ship',
        '069' => 'Promised For Delivery',
        '071' => 'First Arrive',
        '074' => 'Last Arrive',
        '076' => 'Scheduled for Delivery [Week of]',
        '077' => 'Requested For Delivery Week of Date',
        '079' => 'Promised Ship',
        '086' => 'Scheduled for Shipment [Week of]',
        '087' => 'Requested for Shipment [Week of]',
        '097' => 'Document/Transaction Date',
        '100' => 'No Shipping Schedule Established',
        '110' => 'Originally Scheduled Ship',
        '118' => 'Requested Pick Up Date',
        '145' => 'Opening',
        '146' => 'Closing',
        '168' => 'Release',
        '171' => 'Revision',
        '191' => 'Material Specification',
        '196' => 'Start Date',
        '197' => 'End Date',
        '201' => 'Accept By',
        '209' => 'Value Date',
        '220' => 'Payment/Penalty',
        '291' => 'Planned',
        '328' => 'Change',
        '370' => 'Actual Departure Date',
        '371' => 'Estimated Arrival Date',
        '372' => 'Actual Arrival Date',
        '405' => 'Production',
        '511' => 'Shelf Life Expiration',
        '598' => 'Rejected',
        '619' => 'Decision',
        '636' => 'Last Update Date',
        '807' => 'Stored',
        '809' => 'Post',
        '815' => 'Maturity Date',
        '945' => 'Activity',
        '995' => 'Recording',
        'AA1' => 'Estimated Arrival Point',
        'AA2' => 'Estimated Discharge Point',
        'AAH' => 'Offer Expiry',
        'AAL' => 'Installment',
        'ACT' => 'Active Date',
        'APD' => 'Actual Pick Up Date',
        'BBD' => 'Best Before Date',
        'CSD' => 'Contract Signature Date',
        'DLO' => 'Date Loaded',
        'EDC' => 'Authorization',
        'FCS' => 'First Consumer Sales Date',
        'MRB' => 'Must Respond By',
        'ORS' => 'Date Order Received by Supplier',
        'SOL' => 'Sold',
        'TPD' => 'Tax Point Date',
        'TRM' => 'Transmission Date'
    ];

    public function __construct(string $qualifier = null, string $date = null)
    {
        $this->qualifier = $qualifier;
        $this->date = $date;
    }

    public function exportToXml(SimpleXMLElement $parent): SimpleXMLElement
    {
        if (!$this->qualifier || !$this->date) {
            throw new ElementNotSet('Dates: Both DateTimeQualifier and Date must be set.');
        }
        if (!array_key_exists($this->qualifier, $this->qualifiers)) {
            throw new ElementInvalid('Dates: Invalid DateTimeQualifier.');
        }
        $root = $parent->addChild('Dates');
        $root->addChild('DateTimeQualifier', $this->qualifier);
        $root->addChild('Date', $this->formatDate($this->date));
        return $root;
    }

    public function importFromXml(SimpleXMLElement $root): void
    {
        $this->qualifier = (string)$root->DateTimeQualifier;
        $this->date = (string)$root->Date;
    }

    public function qualifierLabel(string $qualifier): string
    {
        return $this->qualifiers[$qualifier] ?? '';
    }

    public function timestamp(): int
    {
        return (int)$this->formatDate($this->date, 'U');
    }
}
