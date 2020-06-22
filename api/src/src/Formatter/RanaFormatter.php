<?php

namespace App\Formatter;

use App\Entity\Rana;
use App\Util\Constants;
use App\Util\Util;

class RanaFormatter
{
    /** @var ChapterFormatter */
    private $chapterFormatter;

    /** @var Constants */
    private $constants;

    /** @var RandaFormatter */
    private $randaFormatter;

    /** RanaFormatter constructor */
    public function __construct(
        ChapterFormatter $chapterFormatter,
        Constants $constants,
        RandaFormatter $randaFormatter
    ) {
        $this->chapterFormatter = $chapterFormatter;
        $this->constants = $constants;
        $this->randaFormatter = $randaFormatter;
    }

    /**
     * @param Rana $rana
     *
     * @return array
     */
    private function format(Rana $rana): array
    {
        $details = [
            'id' => $rana->getId()
        ];

        return $details;
    }

    /**
     * @param Rana $rana
     *
     * @return array
     */
    public function formatBase(Rana $rana): array
    {
        $details = array_merge($this->format($rana), [
            'chapter' => $this->chapterFormatter->formatBase($rana->getChapter()),
            'randa'   => $this->randaFormatter->formatBase($rana->getRanda())
        ]);

        return $details;
    }

    /**
     * @param Rana $rana
     *
     * @return array
     */
    public function formatData(Rana $rana): array
    {
        // $lifeCycle = Util::arrayGetValue($rana->getRanaLifecycles()->toArray(), 0);
        //$currentTimeslot = $lifeCycle->getCurrentTimeslot();
        $currentTimeslot = $rana->getRanda()->getCurrentTimeslot();
        $newMembers = array_filter($rana->getNewMembers()->toArray(), function ($newMember) use($currentTimeslot) {
            return $newMember->getTimeslot() == $currentTimeslot;
        });

        $renewedMembers = array_filter($rana->getRenewedMembers()->toArray(), function ($renewedMember) use($currentTimeslot) {
            return $renewedMember->getTimeslot() == $currentTimeslot;
        });

        $retentions = array_filter($rana->getRetentions()->toArray(), function ($retention) use($currentTimeslot) {
            return $retention->getTimeslot() == $currentTimeslot;
        });

        $newMembersValues = [
            $this->constants::VALUE_TYPE_APPROVED => [],
            $this->constants::VALUE_TYPE_CONSUMPTIVE => [],
            $this->constants::VALUE_TYPE_PROPOSED => []
        ];
        $renewedMembersValues = [
            $this->constants::VALUE_TYPE_APPROVED => [],
            $this->constants::VALUE_TYPE_CONSUMPTIVE => [],
            $this->constants::VALUE_TYPE_PROPOSED => []
        ];
        $retentionsValues = [
            $this->constants::VALUE_TYPE_APPROVED => [],
            $this->constants::VALUE_TYPE_CONSUMPTIVE => [],
            $this->constants::VALUE_TYPE_PROPOSED => []
        ];

        $newMembers = [
            $this->constants::VALUE_TYPE_APPROVED => Util::arrayGetValue(array_filter($newMembers, function ($newMember) {
                return $newMember->getValueType() == $this->constants::VALUE_TYPE_APPROVED;
            }), 0, null),
            $this->constants::VALUE_TYPE_CONSUMPTIVE => Util::arrayGetValue(array_filter($newMembers, function ($newMember) {
                return $newMember->getValueType() == $this->constants::VALUE_TYPE_CONSUMPTIVE;
            }), 0, null),
            $this->constants::VALUE_TYPE_PROPOSED => Util::arrayGetValue(array_filter($newMembers, function ($newMember) {
                return $newMember->getValueType() == $this->constants::VALUE_TYPE_PROPOSED;
            }), 0, null)
        ];

        $renewedMembers = [
            $this->constants::VALUE_TYPE_APPROVED => Util::arrayGetValue(array_filter($renewedMembers, function ($renewedMember) {
                return $renewedMember->getValueType() == $this->constants::VALUE_TYPE_APPROVED;
            }), 0, null),
            $this->constants::VALUE_TYPE_CONSUMPTIVE => Util::arrayGetValue(array_filter($renewedMembers, function ($renewedMember) {
                return $renewedMember->getValueType() == $this->constants::VALUE_TYPE_CONSUMPTIVE;
            }), 0, null),
            $this->constants::VALUE_TYPE_PROPOSED => Util::arrayGetValue(array_filter($renewedMembers, function ($renewedMember) {
                return $renewedMember->getValueType() == $this->constants::VALUE_TYPE_PROPOSED;
            }), 0, null)
        ];
        $retentions = [
            $this->constants::VALUE_TYPE_APPROVED => Util::arrayGetValue(array_filter($retentions, function ($retention) {
                return $retention->getValueType() == $this->constants::VALUE_TYPE_APPROVED;
            }), 0, null),
            $this->constants::VALUE_TYPE_CONSUMPTIVE => Util::arrayGetValue(array_filter($retentions, function ($retention) {
                return $retention->getValueType() == $this->constants::VALUE_TYPE_CONSUMPTIVE;
            }), 0, null),
            $this->constants::VALUE_TYPE_PROPOSED => Util::arrayGetValue(array_filter($retentions, function ($retention) {
                return $retention->getValueType() == $this->constants::VALUE_TYPE_PROPOSED;
            }), 0, null)
        ];

        for ($i = 1; $i <= 12; $i++) {
            $method = "getM$i";
            foreach ([
                $this->constants::VALUE_TYPE_APPROVED,
                $this->constants::VALUE_TYPE_CONSUMPTIVE,
                $this->constants::VALUE_TYPE_PROPOSED
            ] as $type) {
                $newMembersValues[$type]["m$i"] = is_null($newMembers[$type]) ? 0 : $newMembers[$type]->$method() ?? 0;
                $renewedMembersValues[$type]["m$i"] = is_null($renewedMembers[$type]) ? 0 : $renewedMembers[$type]->$method() ?? 0;
                $retentionsValues[$type]["m$i"] = is_null($retentions[$type]) ? 0 : $retentions[$type]->$method() ?? 0;
            }
        }
        $details = array_merge($this->format($rana), [
            'newMembers'     => $newMembersValues,
            'renewedMembers' => $renewedMembersValues,
            'retentions'     => $retentionsValues
        ]);

        return $details;
    }

    /**
     * @param Rana $rana
     *
     * @return array
     */
    public function formatFull(Rana $rana): array
    {
        $details = array_merge($this->format($rana), [
            'chapter' => $this->chapterFormatter->formatFull($rana->getChapter()),
            'randa'   => $this->randaFormatter->formatFull($rana->getRanda())
        ]);

        return $details;
    }
}
