<?php

namespace App\Formatter;

use App\Entity\RenewedMember;

class RenewedMemberFormatter
{
    private const RANA_BASE_DATA = 1;
    private const RANA_FULL_DATA = 0;

    /** @var RanaFormatter */
    private $ranaFormatter;

    /** RenewedMemberFormatter constructor */
    public function __construct(
        RanaFormatter $ranaFormatter
    ) {
        $this->ranaFormatter = $ranaFormatter;
    }

    /**
     * @param RenewedMember $renewedMember
     *
     * @return array
     */
    private function format(RenewedMember $renewedMember, $ranaDataType): array
    {
        $details = [
            'id'        => $renewedMember->getId(),
            'm1'        => $renewedMember->getM1(),
            'm2'        => $renewedMember->getM2(),
            'm3'        => $renewedMember->getM3(),
            'm4'        => $renewedMember->getM4(),
            'm5'        => $renewedMember->getM5(),
            'm6'        => $renewedMember->getM6(),
            'm7'        => $renewedMember->getM7(),
            'm8'        => $renewedMember->getM8(),
            'm9'        => $renewedMember->getM9(),
            'm10'       => $renewedMember->getM10(),
            'm11'       => $renewedMember->getM11(),
            'm12'       => $renewedMember->getM12(),
            'rana'      => $ranaDataType == self::RANA_BASE_DATA ? $this->ranaFormatter->formatBase($renewedMember->getRana()) : $this->ranaFormatter->formatFull($renewedMember->getRana()),
            'timeslot'  => $renewedMember->getTimeslot(),
            'valueType' => $renewedMember->getValueType(),
        ];

        return $details;
    }

    /**
     * @param RenewedMember $renewedMember
     *
     * @return array
     */
    public function formatBase(RenewedMember $renewedMember): array
    {
        return $this->format($renewedMember, self::RANA_BASE_DATA);
    }

    /**
     * @param RenewedMember $renewedMember
     *
     * @return array
     */
    public function formatFull(RenewedMember $renewedMember): array
    {
        return $this->format($renewedMember, self::RANA_FULL_DATA);
    }

    /**
     * @param RenewedMember $renewedMember
     *
     * @return array
     */
    public function formatNoRana(RenewedMember $renewedMember): array
    {
        $details = $this->format($renewedMember, self::RANA_BASE_DATA);
        unset($details['rana']);
        return $details;
    }
}
