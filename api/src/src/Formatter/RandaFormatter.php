<?php

namespace App\Formatter;

use App\Entity\Randa;

class RandaFormatter
{
    private const REGION_BASE_DATA = 1;
    private const REGION_FULL_DATA = 0;

    /** @var RegionFormatter */
    private $regionFormatter;

    /** RandaFormatter constructor */
    public function __construct(
        RegionFormatter $regionFormatter
    ) {
        $this->regionFormatter = $regionFormatter;
    }

    /**
     * @param Randa $randa
     *
     * @return array
     */
    private function format(Randa $randa, $regionDataType): array
    {
        $details = [
            'currentTimeslot' => $randa->getCurrentTimeslot(),
            'id'              => $randa->getId(),
            'region'          => $regionDataType == self::REGION_BASE_DATA ? $this->regionFormatter->formatBase($randa->getRegion()) : $this->regionFormatter->formatFull($randa->getRegion()),
            'year'            => $randa->getYear()
        ];

        return $details;
    }

    /**
     * @param Randa $randa
     *
     * @return array
     */
    public function formatBase(Randa $randa): array
    {
        return $this->format($randa, self::REGION_BASE_DATA);
    }

    /**
     * @param Randa $randa
     *
     * @return array
     */
    public function formatFull(Randa $randa): array
    {
        return $this->format($randa, self::REGION_FULL_DATA);
    }
}
