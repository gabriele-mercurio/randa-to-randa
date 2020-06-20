<?php

namespace App\Formatter;

use App\Entity\Region;

class RegionFormatter
{
    /** RegionFormatter constructor */
    public function __construct()
    {
    }

    /**
     * @param Region $region
     *
     * @return array
     */
    private function format(Region $region): array
    {
        $details = [
            'id'    => $region->getId(),
            'name'  => $region->getName()
        ];

        return $details;
    }

    /**
     * @param Region $region
     *
     * @return array
     */
    public function formatBase(Region $region): array
    {
        return $this->format($region);
    }



    /**
     * @param Region $region
     *
     * @return array
     */
    public function formatWithRole(Region $region, String $role): array
    {
        return  array_merge($this->format($region), [
            'role' => $role
        ]);
    }


    /**
     * @param Region $region
     *
     * @return array
     */
    public function formatFull(Region $region): array
    {
        return array_merge($this->format($region), [
            'notes' => $region->getNotes()
        ]);
    }
}
