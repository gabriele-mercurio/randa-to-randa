<?php

namespace App\Formatter;

use App\Entity\RanaLifecycle;

class RanaLifecycleFormatter
{
    private const RANA_BASE_DATA = 1;
    private const RANA_FULL_DATA = 0;

    /** @var RanaFormatter */
    private $ranaFormatter;

    /** RanaLifecycleFormatter constructor */
    public function __construct(
        RanaFormatter $ranaFormatter
    ) {
        $this->ranaFormatter = $ranaFormatter;
    }

    /**
     * @param RanaLifecycle $ranaLifecycle
     *
     * @return array
     */
    private function format(RanaLifecycle $ranaLifecycle, $ranaDataType): array
    {
        $details = [
            'currentStatus'   => $ranaLifecycle->getCurrentStatus(),
            'currentTimeslot' => $ranaLifecycle->getCurrentTimeslot(),
            'id'              => $ranaLifecycle->getId(),
            'rana'            => $ranaDataType == self::RANA_BASE_DATA ? $this->ranaFormatter->formatBase($ranaLifecycle->getRana()) : $this->ranaFormatter->formatFull($ranaLifecycle->getRana())
        ];

        return $details;
    }

    /**
     * @param RanaLifecycle $ranaLifecycle
     *
     * @return array
     */
    public function formatBase(RanaLifecycle $ranaLifecycle): array
    {
        return $this->format($ranaLifecycle, self::RANA_BASE_DATA);
    }

    /**
     * @param RanaLifecycle $ranaLifecycle
     *
     * @return array
     */
    public function formatFull(RanaLifecycle $ranaLifecycle): array
    {
        return $this->format($ranaLifecycle, self::RANA_FULL_DATA);
    }
}
