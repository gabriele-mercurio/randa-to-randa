<?php

namespace App\Formatter;

use App\Entity\Director;

class DirectorFormatter
{
    /** DirectorFormatter constructor */
    public function __construct()
    {
    }

    /**
     * @param Director $director
     *
     * @return array
     */
    private function format(Director $director): array
    {
        $details = [
            'areaPercentage'        => $director->getAreaPercentage(),
            'email'                 => $director->getUser()->getEmail(),
            'firstName'             => $director->getUser()->getFirstName(),
            'fixedPercentage'       => $director->getFixedPercentage(),
            'fullName'              => $director->getUser()->getFullName(),
            'greenLightPercentage'  => $director->getGreenLightPercentage(),
            'greyLightPercentage'   => $director->getGreyLightPercentage(),
            'id'                    => $director->getId(),
            'lastName'              => $director->getUser()->getLastName(),
            'launchPercentage'      => $director->getLaunchPercentage(),
            'payType'               => $director->getPayType(),
            'redLightPercentage'    => $director->getRedLightPercentage(),
            'role'                  => $director->getRole(),
            'supervisor'            => $director->getSupervisor() ? $this->formatBase($director->getSupervisor()) : null,
            'yellowLightPercentage' => $director->getYellowLightPercentage()
        ];

        return $details;
    }

    /**
     * @param Director $director
     *
     * @return array
     */
    public function formatBase(Director $director): array
    {
        $details = [
            'fullName' => $director->getUser()->getFullName(),
            'id'       => $director->getId()
        ];

        return $details;
    }

    /**
     * @param Director $director
     *
     * @return array
     */
    public function formatFull(Director $director): array
    {
        return $this->format($director);
    }
}
