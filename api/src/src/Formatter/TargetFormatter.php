<?php

namespace App\Formatter;

use App\Entity\Target;

class TargetFormatter
{
    /** TargetFormatter constructor */
    public function __construct()
    {
    }

    /**
     * @param Target $target
     *
     * @return array
     */
    private function format(Target $target): array
    {
        $details = [
            'id'   => $target->getId(),
            'name' => $target->getName()
        ];

        return $details;
    }

    /**
     * @param Target $target
     *
     * @return array
     */
    public function formatBase(Target $target): array
    {
        return $this->format($target);
    }

    /**
     * @param Target $target
     *
     * @return array
     */
    public function formatFull(Target $target): array
    {
        return $this->format($target);
    }
}
