<?php

namespace App\Formatter;

use App\Entity\Rana;
use App\Util\Constants;
use App\Util\Util;

class RanaFormatter
{
    /** @var ChapterFormatter */
    private $chapterFormatter;

    /** @var RandaFormatter */
    private $randaFormatter;

    /** RanaFormatter constructor */
    public function __construct(
        ChapterFormatter $chapterFormatter,
        RandaFormatter $randaFormatter
    ) {
        $this->chapterFormatter = $chapterFormatter;
        $this->randaFormatter = $randaFormatter;
    }

    private static function divideByValueTypes(array $objects): array
    {
        return [
            Constants::VALUE_TYPE_APPROVED => Util::arrayGetValue(array_filter($objects, function ($object) {
                return $object->getValueType() == Constants::VALUE_TYPE_APPROVED;
            }), 0, null),
            Constants::VALUE_TYPE_CONSUMPTIVE => Util::arrayGetValue(array_filter($objects, function ($object) {
                return $object->getValueType() == Constants::VALUE_TYPE_CONSUMPTIVE;
            }), 0, null),
            Constants::VALUE_TYPE_PROPOSED => Util::arrayGetValue(array_filter($objects, function ($object) {
                return $object->getValueType() == Constants::VALUE_TYPE_PROPOSED;
            }), 0, null)
        ];
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
    public function formatData(Rana $rana, string $role): array
    {
        $allDetails = [];
        $newMembersValues = $renewedMembersValues = $retentionsValues = [
            Constants::VALUE_TYPE_PROPOSED => []
        ];
        $types = [
            Constants::VALUE_TYPE_PROPOSED
        ];

        if ($role != Constants::ROLE_ASSISTANT) {
            $newMembersValues = array_merge($newMembersValues, [
                Constants::VALUE_TYPE_APPROVED => [],
                Constants::VALUE_TYPE_CONSUMPTIVE => []
            ]);
            $renewedMembersValues = array_merge($renewedMembersValues, [
                Constants::VALUE_TYPE_APPROVED => [],
                Constants::VALUE_TYPE_CONSUMPTIVE => []
            ]);
            $retentionsValues = array_merge($retentionsValues, [
                Constants::VALUE_TYPE_APPROVED => [],
                Constants::VALUE_TYPE_CONSUMPTIVE => []
            ]);
            $types = array_merge($types, [
                Constants::VALUE_TYPE_APPROVED,
                Constants::VALUE_TYPE_CONSUMPTIVE
            ]);
        }

        $lifeCycles = $rana->getRanaLifecycles()->toArray();

        foreach ($lifeCycles as $lifeCycle) {
            $currentState = $lifeCycle->getCurrentState();
            $currentTimeslot = $lifeCycle->getCurrentTimeslot();

            $newMembers = static::getCurrentTimeslotData($rana->getNewMembers()->toArray(), $currentTimeslot);
            $newMembers = static::divideByValueTypes($newMembers);

            $renewedMembers = static::getCurrentTimeslotData($rana->getRenewedMembers()->toArray(), $currentTimeslot);
            $renewedMembers = static::divideByValueTypes($renewedMembers);

            $retentions = static::getCurrentTimeslotData($rana->getRetentions()->toArray(), $currentTimeslot);
            $retentions = static::divideByValueTypes($retentions);

            for ($i = 1; $i <= 12; $i++) {
                $method = "getM$i";
                foreach ($types as $type) {
                    $newMembersValues[$type]["m$i"] = is_null($newMembers[$type]) ? 0 : $newMembers[$type]->$method() ?? 0;
                    $renewedMembersValues[$type]["m$i"] = is_null($renewedMembers[$type]) ? 0 : $renewedMembers[$type]->$method() ?? 0;
                    $retentionsValues[$type]["m$i"] = is_null($retentions[$type]) ? 0 : $retentions[$type]->$method() ?? 0;
                }
            }

            $allDetails[] = array_merge($this->format($rana), [
                'newMembers'     => $newMembersValues,
                'renewedMembers' => $renewedMembersValues,
                'retentions'     => $retentionsValues,
                'timeslot'       => $currentTimeslot,
                'state'          => $currentState
            ]);
        }

        return $allDetails;
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

    private static function getCurrentTimeslotData(array $objects, string $currentTimeslot): array
    {
        return array_filter($objects, function ($object) use ($currentTimeslot) {
            return $object->getTimeslot() == $currentTimeslot;
        });
    }
}
