<?php

namespace App\Formatter;

use App\Entity\TrafficLight;

class TrafficLightFormatter
{
    private const RANA_BASE_DATA = 1;
    private const RANA_FULL_DATA = 0;

    /** @var RanaFormatter */
    private $ranaFormatter;

    /** TrafficLightFormatter constructor */
    public function __construct(
        RanaFormatter $ranaFormatter
    ) {
        $this->ranaFormatter = $ranaFormatter;
    }

    /**
     * @param TrafficLight $trafficLight
     *
     * @return array
     */
    private function format(TrafficLight $trafficLight, $ranaDataType): array
    {
        $details = [
            'id'        => $trafficLight->getId(),
            'm1'        => $trafficLight->getM1(),
            'm2'        => $trafficLight->getM2(),
            'm3'        => $trafficLight->getM3(),
            'm4'        => $trafficLight->getM4(),
            'm5'        => $trafficLight->getM5(),
            'm6'        => $trafficLight->getM6(),
            'm7'        => $trafficLight->getM7(),
            'm8'        => $trafficLight->getM8(),
            'm9'        => $trafficLight->getM9(),
            'm10'       => $trafficLight->getM10(),
            'm11'       => $trafficLight->getM11(),
            'm12'       => $trafficLight->getM11(),
            'rana'      => $ranaDataType == self::RANA_BASE_DATA ? $this->ranaFormatter->formatBase($trafficLight->getRana()) : $this->ranaFormatter->formatFull($trafficLight->getRana()),
            'timeslot'  => $trafficLight->getTimeslot()
        ];

        return $details;
    }

    /**
     * @param TrafficLight $trafficLight
     *
     * @return array
     */
    public function formatBase(TrafficLight $trafficLight): array
    {
        return $this->format($trafficLight, self::RANA_BASE_DATA);
    }

    /**
     * @param TrafficLight $trafficLight
     *
     * @return array
     */
    public function formatFull(TrafficLight $trafficLight): array
    {
        return $this->format($trafficLight, self::RANA_FULL_DATA);
    }
}
