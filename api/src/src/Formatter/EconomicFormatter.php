<?php

namespace App\Formatter;

use App\Entity\Economic;

class EconomicFormatter
{
    private const RANDA_BASE_DATA = 1;
    private const RANDA_FULL_DATA = 0;

    /** @var RandaFormatter */
    private $randaFormatter;

    /** EconomicFormatter constructor */
    public function __construct(
        RandaFormatter $randaFormatter
    ) {
        $this->randaFormatter = $randaFormatter;
    }

    /**
     * @param Economic $economic
     *
     * @return array
     */
    private function format(Economic $economic, $randaDataType): array
    {
        $details = [
            'deprecations'     => $economic->getDeprecations(),
            'extraIncomings'   => $economic->getExtraIncomings(),
            'financialCharges' => $economic->getFinancialCharges(),
            'id'               => $economic->getId(),
            'provisions'       => $economic->getProvisions(),
            'randa'            => $randaDataType == self::RANDA_BASE_DATA ? $this->randaFormatter->formatBase($economic->getRanda()) : $this->randaFormatter->formatFull($economic->getRanda()),
            'tax'              => $economic->getTax(),
            'timeslot'         => $economic->getTimeslot(),
            'year'             => $economic->getYear()
        ];

        return $details;
    }

    /**
     * @param Economic $economic
     *
     * @return array
     */
    public function formatBase(Economic $economic): array
    {
        return $this->format($economic, self::RANDA_BASE_DATA);
    }

    /**
     * @param Economic $economic
     *
     * @return array
     */
    public function formatFull(Economic $economic): array
    {
        return $this->format($economic, self::RANDA_FULL_DATA);
    }
}
