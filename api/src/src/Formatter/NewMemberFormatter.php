<?php

namespace App\Formatter;

use App\Entity\NewMember;

class NewMemberFormatter
{
    private const RANA_BASE_DATA = 1;
    private const RANA_FULL_DATA = 0;

    /** @var RanaFormatter */
    private $ranaFormatter;

    /** NewMemberFormatter constructor */
    public function __construct(
        RanaFormatter $ranaFormatter
    ) {
        $this->ranaFormatter = $ranaFormatter;
    }

    /**
     * @param NewMember $newMember
     *
     * @return array
     */
    private function format(NewMember $newMember, $ranaDataType): array
    {
        $details = [
            'id'        => $newMember->getId(),
            'm1'        => $newMember->getM1(),
            'm2'        => $newMember->getM2(),
            'm3'        => $newMember->getM3(),
            'm4'        => $newMember->getM4(),
            'm5'        => $newMember->getM5(),
            'm6'        => $newMember->getM6(),
            'm7'        => $newMember->getM7(),
            'm8'        => $newMember->getM8(),
            'm9'        => $newMember->getM9(),
            'm10'       => $newMember->getM10(),
            'm11'       => $newMember->getM11(),
            'm12'       => $newMember->getM11(),
            'rana'      => $ranaDataType == self::RANA_BASE_DATA ? $this->ranaFormatter->formatBase($newMember->getRana()) : $this->ranaFormatter->formatFull($newMember->getRana()),
            'timeslot'  => $newMember->getTimeslot(),
            'valueType' => $newMember->getValueType()
        ];

        return $details;
    }

    /**
     * @param NewMember $newMember
     *
     * @return array
     */
    public function formatBase(NewMember $newMember): array
    {
        return $this->format($newMember, self::RANA_BASE_DATA);
    }

    /**
     * @param NewMember $newMember
     *
     * @return array
     */
    public function formatFull(NewMember $newMember): array
    {
        return $this->format($newMember, self::RANA_FULL_DATA);
    }
}
