<?php

namespace App\Formatter;

use App\Entity\StrategyPerRanda;

class StrategyPerRandaFormatter
{
    /** @var RandaFormatter */
    private $randaFormatter;

    /** @var StrategyFormatter */
    private $strategyFormatter;

    /** StrategyPerRandaFormatter constructor */
    public function __construct(
        RandaFormatter $randaFormatter,
        StrategyFormatter $strategyFormatter
    ) {
        $this->randaFormatter = $randaFormatter;
        $this->strategyFormatter = $strategyFormatter;
    }

    /**
     * @param StrategyPerRanda $strategyPerRanda
     *
     * @return array
     */
    private function format(StrategyPerRanda $strategyPerRanda): array
    {
        $details = [
            'id'       => $strategyPerRanda->getId()
        ];

        return $details;
    }

    /**
     * @param StrategyPerRanda $strategyPerRanda
     *
     * @return array
     */
    public function formatBase(StrategyPerRanda $strategyPerRanda): array
    {
        return array_merge($this->format($strategyPerRanda), [
            'randa'    => $this->randaFormatter->formatBase($strategyPerRanda->getRanda()),
            'strategy' => $this->strategyFormatter->formatBase($strategyPerRanda->getStrategy())
        ]);
    }

    /**
     * @param StrategyPerRanda $strategyPerRanda
     *
     * @return array
     */
    public function formatFull(StrategyPerRanda $strategyPerRanda): array
    {
        return array_merge($this->format($strategyPerRanda), [
            'randa'    => $this->randaFormatter->formatFull($strategyPerRanda->getRanda()),
            'strategy' => $this->strategyFormatter->formatFull($strategyPerRanda->getStrategy())
        ]);
    }
}
