<?php

namespace App\Formatter;

use App\Entity\Strategy;

class StrategyFormatter
{
    private const TARGET_BASE_DATA = 1;
    private const TARGET_FULL_DATA = 0;

    /** @var TargetFormatter */
    private $targetFormatter;

    /** StrategyFormatter constructor */
    public function __construct(
        TargetFormatter $targetFormatter
    ) {
        $this->targetFormatter = $targetFormatter;
    }

    /**
     * @param Strategy $strategy
     *
     * @return array
     */
    private function format(Strategy $strategy, $targetDataType): array
    {
        $details = [
            'description' => $strategy->getDescription(),
            'id'          => $strategy->getId(),
            'target'      => $targetDataType == self::TARGET_BASE_DATA ? $this->targetFormatter->formatBase($strategy->getTarget()) : $this->targetFormatter->formatFull($strategy->getTarget()),
            'timestamp'   => $strategy->getTimestamp()->format("d-m-Y")
        ];

        return $details;
    }

    /**
     * @param Strategy $strategy
     *
     * @return array
     */
    public function formatBase(Strategy $strategy): array
    {
        return $this->format($strategy, self::TARGET_BASE_DATA);
    }

    /**
     * @param Strategy $strategy
     *
     * @return array
     */
    public function formatFull(Strategy $strategy): array
    {
        return $this->format($strategy, self::TARGET_FULL_DATA);
    }
}
