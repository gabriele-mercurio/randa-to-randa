<?php

namespace App\Formatter;

use App\Entity\RevenueCost;

class RevenueCostFormatter
{
    /** RevenueCostFormatter constructor */
    public function __construct()
    {
    }

    /**
     * @param RevenueCost $revenueCost
     *
     * @return array
     */
    private function format(RevenueCost $revenueCost): array
    {
        $details = [
            'id'    => $revenueCost->getId(),
            'type'  => $revenueCost->getType(),
            'value' => $revenueCost->getValue()
        ];

        return $details;
    }

    /**
     * @param RevenueCost $revenueCost
     *
     * @return array
     */
    public function formatBase(RevenueCost $revenueCost): array
    {
        return $this->format($revenueCost);
    }

    /**
     * @param RevenueCost $revenueCost
     *
     * @return array
     */
    public function formatFull(RevenueCost $revenueCost): array
    {
        return $this->format($revenueCost);
    }
}
