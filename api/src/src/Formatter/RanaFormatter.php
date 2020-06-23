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
        $lifeCycle = Util::arrayGetValue($rana->getRanaLifecycles()->toArray(), 0);
        $currentTimeslot = $lifeCycle->getCurrentTimeslot();

        $newMembers = static::getCurrentTimeslotData($rana->getNewMembers()->toArray(), $currentTimeslot);
        $renewedMembers = static::getCurrentTimeslotData($rana->getRenewedMembers()->toArray(), $currentTimeslot);
        $retentions = static::getCurrentTimeslotData($rana->getRetentions()->toArray(), $currentTimeslot);

        $newMembersValues = $renewedMembersValues = $retentionsValues = [
            $this->constants::VALUE_TYPE_PROPOSED => []
        ];

        $types = [
            $this->constants::VALUE_TYPE_PROPOSED
        ];

        if ($role != $this->constants::ROLE_ASSISTANT) {
            $types = array_merge($types, [
                $this->constants::VALUE_TYPE_APPROVED,
                $this->constants::VALUE_TYPE_CONSUMPTIVE
            ]);
            $newMembersValues = array_merge($newMembersValues, [
                $this->constants::VALUE_TYPE_APPROVED => [],
                $this->constants::VALUE_TYPE_CONSUMPTIVE => []
            ]);
            $renewedMembersValues = array_merge($renewedMembersValues, [
                $this->constants::VALUE_TYPE_APPROVED => [],
                $this->constants::VALUE_TYPE_CONSUMPTIVE => []
            ]);
            $retentionsValues = array_merge($retentionsValues, [
                $this->constants::VALUE_TYPE_APPROVED => [],
                $this->constants::VALUE_TYPE_CONSUMPTIVE => []
            ]);
        }

        $newMembers = static::divideByValueTypes($newMembers);
        $renewedMembers = static::divideByValueTypes($renewedMembers);
        $retentions = static::divideByValueTypes($retentions);

        for ($i = 1; $i <= 12; $i++) {
            $method = "getM$i";
            foreach ($types as $type) {
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

    private static function getCurrentTimeslotData(array $objects, string $currentTimeslot): array
    {
        return array_filter($objects, function ($object) use($currentTimeslot) {
            return $object->getTimeslot() == $currentTimeslot;
        });
    }
}
