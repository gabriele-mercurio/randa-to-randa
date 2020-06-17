<?php

namespace App\Formatter;

use App\Entity\Rana;
use App\Repository\NewMemberRepository;
use App\Repository\RenewedMemberRepository;
use App\Repository\RetentionRepository;
use App\Util\Util;

class RanaFormatter
{
    /** @var ChapterFormatter */
    private $chapterFormatter;

    /** @var NewMemberRepository */
    private $newMemberRepository;

    /** @var RandaFormatter */
    private $randaFormatter;

    /** @var RenewedMemberRepository */
    private $renewedMemberRepository;

    /** @var RetentionRepository */
    private $retentionRepository;

    /** RanaFormatter constructor */
    public function __construct(
        ChapterFormatter $chapterFormatter,
        NewMemberRepository $newMemberRepository,
        RandaFormatter $randaFormatter,
        RenewedMemberRepository $renewedMemberRepository,
        RetentionRepository $retentionRepository
    ) {
        $this->chapterFormatter = $chapterFormatter;
        $this->newMemberRepository = $newMemberRepository;
        $this->randaFormatter = $randaFormatter;
        $this->renewedMemberRepository = $renewedMemberRepository;
        $this->retentionRepository = $retentionRepository;
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
        $lifeCycle = Util::arrayGetValue($rana->getRanaLifecycles()->toArray(), 0);
        $currentTimeslot = $lifeCycle->getCurrentTimeslot();
        $newMembers = array_filter($rana->getNewMembers()->toArray(), function ($newMember) use($currentTimeslot) {
            return $newMember->getTimeslot() == $currentTimeslot;
        });
        $renewedMembers = array_filter($rana->getRenewedMembers()->toArray(), function ($renewedMember) use($currentTimeslot) {
            return $renewedMember->getTimeslot() == $currentTimeslot;
        });
        $retentions = $rana->getRetentions()->toArray();
        $newMembersValues = [
            $this->newMemberRepository::NEW_MEMBER_VALUE_TYPE_APPROVED => [],
            $this->newMemberRepository::NEW_MEMBER_VALUE_TYPE_CONSUMPTIVE => [],
            $this->newMemberRepository::NEW_MEMBER_VALUE_TYPE_PROPOSED => []
        ];
        $renewedMembersValues = [
            $this->renewedMemberRepository::RENEWED_MEMBER_VALUE_TYPE_APPROVED => [],
            $this->renewedMemberRepository::RENEWED_MEMBER_VALUE_TYPE_CONSUMPTIVE => [],
            $this->renewedMemberRepository::RENEWED_MEMBER_VALUE_TYPE_PROPOSED => []
        ];
        $retentionsValues = [
            $this->retentionRepository::RETENTION_VALUE_TYPE_APPROVED => [],
            $this->retentionRepository::RETENTION_VALUE_TYPE_CONSUMPTIVE => [],
            $this->retentionRepository::RETENTION_VALUE_TYPE_PROPOSED => []
        ];

        $newMembers = [
            $this->newMemberRepository::NEW_MEMBER_VALUE_TYPE_APPROVED => Util::arrayGetValue(array_filter($newMembers, function ($newMember) {
                return $newMember->getValueType() == $this->newMemberRepository::NEW_MEMBER_VALUE_TYPE_APPROVED;
            }), 0, null),
            $this->newMemberRepository::NEW_MEMBER_VALUE_TYPE_CONSUMPTIVE => Util::arrayGetValue(array_filter($newMembers, function ($newMember) {
                return $newMember->getValueType() == $this->newMemberRepository::NEW_MEMBER_VALUE_TYPE_CONSUMPTIVE;
            }), 0, null),
            $this->newMemberRepository::NEW_MEMBER_VALUE_TYPE_PROPOSED => Util::arrayGetValue(array_filter($newMembers, function ($newMember) {
                return $newMember->getValueType() == $this->newMemberRepository::NEW_MEMBER_VALUE_TYPE_PROPOSED;
            }), 0, null)
        ];
        $renewedMembers = [
            $this->renewedMemberRepository::RENEWED_MEMBER_VALUE_TYPE_APPROVED => Util::arrayGetValue(array_filter($renewedMembers, function ($renewedMember) {
                return $renewedMember->getValueType() == $this->renewedMemberRepository::RENEWED_MEMBER_VALUE_TYPE_APPROVED;
            }), 0, null),
            $this->renewedMemberRepository::RENEWED_MEMBER_VALUE_TYPE_CONSUMPTIVE => Util::arrayGetValue(array_filter($renewedMembers, function ($renewedMember) {
                return $renewedMember->getValueType() == $this->renewedMemberRepository::RENEWED_MEMBER_VALUE_TYPE_CONSUMPTIVE;
            }), 0, null),
            $this->renewedMemberRepository::RENEWED_MEMBER_VALUE_TYPE_PROPOSED => Util::arrayGetValue(array_filter($renewedMembers, function ($renewedMember) {
                return $renewedMember->getValueType() == $this->renewedMemberRepository::RENEWED_MEMBER_VALUE_TYPE_PROPOSED;
            }), 0, null)
        ];
        $retentions = [
            $this->retentionRepository::RETENTION_VALUE_TYPE_APPROVED => Util::arrayGetValue(array_filter($retentions, function ($retention) {
                return $retention->getValueType() == $this->retentionRepository::RETENTION_VALUE_TYPE_APPROVED;
            }), 0, null),
            $this->retentionRepository::RETENTION_VALUE_TYPE_CONSUMPTIVE => Util::arrayGetValue(array_filter($retentions, function ($retention) {
                return $retention->getValueType() == $this->retentionRepository::RETENTION_VALUE_TYPE_CONSUMPTIVE;
            }), 0, null),
            $this->retentionRepository::RETENTION_VALUE_TYPE_PROPOSED => Util::arrayGetValue(array_filter($retentions, function ($retention) {
                return $retention->getValueType() == $this->retentionRepository::RETENTION_VALUE_TYPE_PROPOSED;
            }), 0, null)
        ];
        for ($i = 1; $i <= 12; $i++) {
            $method = "getM$i";
            foreach ([
                $this->newMemberRepository::NEW_MEMBER_VALUE_TYPE_APPROVED,
                $this->newMemberRepository::NEW_MEMBER_VALUE_TYPE_CONSUMPTIVE,
                $this->newMemberRepository::NEW_MEMBER_VALUE_TYPE_PROPOSED
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
