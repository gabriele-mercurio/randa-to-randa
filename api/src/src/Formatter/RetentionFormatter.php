<?php

namespace App\Formatter;

use App\Entity\Retention;

class RetentionFormatter
{
    private const RANA_BASE_DATA = 1;
    private const RANA_FULL_DATA = 0;

    /** @var RanaFormatter */
    private $ranaFormatter;

    /** RetentionFormatter constructor */
    public function __construct(
        RanaFormatter $ranaFormatter
    ) {
        $this->ranaFormatter = $ranaFormatter;
    }

    /**
     * @param Retention $retention
     *
     * @return array
     */
    private function format(Retention $retention, $ranaDataType): array
    {
        $details = [
            'id'        => $retention->getId(),
            'm1'        => $retention->getM1(),
            'm2'        => $retention->getM2(),
            'm3'        => $retention->getM3(),
            'm4'        => $retention->getM4(),
            'm5'        => $retention->getM5(),
            'm6'        => $retention->getM6(),
            'm7'        => $retention->getM7(),
            'm8'        => $retention->getM8(),
            'm9'        => $retention->getM9(),
            'm10'       => $retention->getM10(),
            'm11'       => $retention->getM11(),
            'm12'       => $retention->getM12(),
            'rana'      => $ranaDataType == self::RANA_BASE_DATA ? $this->ranaFormatter->formatBase($retention->getRana()) : $this->ranaFormatter->formatFull($retention->getRana()),
            'timeslot'  => $retention->getTimeslot(),
            'valueType' => $retention->getValueType()
        ];

        return $details;
    }

    /**
     * @param Retention $retention
     *
     * @return array
     */
    public function formatBase(Retention $retention): array
    {
        return $this->format($retention, self::RANA_BASE_DATA);
    }

    /**
     * @param Retention $retention
     *
     * @return array
     */
    public function formatFull(Retention $retention): array
    {
        return $this->format($retention, self::RANA_FULL_DATA);
    }

    /**
     * @param Retention $retention
     *
     * @return array
     */
    public function formatNoRana(Retention $retention): array
    {
        $details = $this->format($retention, self::RANA_BASE_DATA);
        unset($details['rana']);
        return $details;
    }
}
