<?php

namespace App\Formatter;

use App\Util\Util;
use App\Entity\Rana;
use App\Util\Constants;
use App\Repository\NewMemberRepository;
use App\Repository\RetentionRepository;
use App\Repository\RanaLifecycleRepository;

class RanaFormatter
{
    /** @var ChapterFormatter */
    private $chapterFormatter;

    /** @var NewMemberFormatter */
    private $newMemberFormatter;

    /** @var NewMembersRepository */
    private $newMembersRepository;

    /** @var RanaLifecycleRepository */
    private $ranaLifecycleRepository;

    /** @var RandaFormatter */
    private $randaFormatter;

    /** @var RenewedMemberFormatter */
    private $renewedMemberFormatter;

    /** @var RetentionFormatter */
    private $retentionFormatter;

    /** @var RetentionsRepository */
    private $retentionsRepository;

    /** RanaFormatter constructor */
    public function __construct(
        ChapterFormatter $chapterFormatter,
        // NewMemberFormatter $newMemberFormatter,
        NewMemberRepository $newMembersRepository,
        RanaLifecycleRepository $ranaLifecycleRepository,
        // RandaFormatter $randaFormatter,
        // RenewedMemberFormatter $renewedMemberFormatter,
        // RetentionFormatter $retentionFormatter,
        RetentionRepository $retentionsRepository
    ) {
        $this->chapterFormatter = $chapterFormatter;
        // $this->newMemberFormatter = $newMemberFormatter;
        $this->newMembersRepository = $newMembersRepository;
        $this->ranaLifecycleRepository = $ranaLifecycleRepository;
        // $this->randaFormatter = $randaFormatter;
        // $this->renewedMemberFormatter = $renewedMemberFormatter;
        // $this->retentionFormatter = $retentionFormatter;
        $this->retentionsRepository = $retentionsRepository;
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
    public function formatCustomData(Rana $rana): array
    {
        // $allDetails[] = array_merge($this->format($rana), [
        //     'newMembers'     => $this->newMemberFormatter->formatNoRana($rana->filteredNewMembers),
        //     'renewedMembers' => $this->renewedMemberFormatter->formatNoRana($rana->filteredRenewedMembers),
        //     'retentions'     => $this->retentionFormatter->formatNoRana($rana->filteredRetentionMembers),
        //     'timeslot'       => $rana->filteredNewMembers->getTimeslot()
        // ]);
        // return $allDetails;
        return [];
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

        // if ($role != Constants::ROLE_ASSISTANT) {
        //     $newMembersValues = array_merge($newMembersValues, [
        //         Constants::VALUE_TYPE_APPROVED => [],
        //         Constants::VALUE_TYPE_CONSUMPTIVE => []
        //     ]);
        //     $renewedMembersValues = array_merge($renewedMembersValues, [
        //         Constants::VALUE_TYPE_APPROVED => [],
        //         Constants::VALUE_TYPE_CONSUMPTIVE => []
        //     ]);
        //     $retentionsValues = array_merge($retentionsValues, [
        //         Constants::VALUE_TYPE_APPROVED => [],
        //         Constants::VALUE_TYPE_CONSUMPTIVE => []
        //     ]);
        //     $types = array_merge($types, [
        //         Constants::VALUE_TYPE_APPROVED,
        //         Constants::VALUE_TYPE_CONSUMPTIVE
        //     ]);
        // }

        $lifeCycles = $rana->getRanaLifecycles();
        foreach ($lifeCycles as $lifeCycle) {
            $timeslot = $lifeCycle->getCurrentTimeslot();
            $state = $lifeCycle->getCurrentState();
            $values_per_type = [
                "newMembers" => [
                    "APPR" => [],
                    "PROP" => [],
                    "CONS" => [
                        "m1" => null,
                        "m2" => null,
                        "m3" => null,
                        "m4" => null,
                        "m5" => null,
                        "m6" => null,
                        "m7" => null,
                        "m8" => null,
                        "m9" => null,
                        "m10" => null,
                        "m11" => null,
                        "m12" => null

                    ],
                ],
                "retentions" => [
                    "APPR" => [],
                    "PROP" => [],
                    "CONS" => [
                        "m1" => null,
                        "m2" => null,
                        "m3" => null,
                        "m4" => null,
                        "m5" => null,
                        "m6" => null,
                        "m7" => null,
                        "m8" => null,
                        "m9" => null,
                        "m10" => null,
                        "m11" => null,
                        "m12" => null
                    ]
                ],
                "currentMembers" => [
                    "m1" => null,
                    "m2" => null,
                    "m3" => null,
                    "m4" => null,
                    "m5" => null,
                    "m6" => null,
                    "m7" => null,
                    "m8" => null,
                    "m9" => null,
                    "m10" => null,
                    "m11" => null,
                    "m12" => null
                ]
            ];


            //if i'am not assistant and there's no proposal, take the apporved
            $valueType = Constants::VALUE_TYPE_PROPOSED;
            if ($role != Constants::ROLE_ASSISTANT) {
                $proposed = $this->ranaLifecycleRepository->findOneBy([
                    "rana" => $rana,
                    "currentState" => "PROPOSED",
                    "currentTimeslot" => $timeslot
                ]);
                if (!$proposed) {
                    $valueType = Constants::VALUE_TYPE_APPROVED;
                }
            }

            $newMembers = $this->newMembersRepository->findBy([
                "rana" => $rana,
                "timeslot" => $timeslot,
                "valueType" => $valueType
            ]);
            $retentions = $this->retentionsRepository->findBy([
                "rana" => $rana,
                "timeslot" => $timeslot,
                "valueType" => $valueType
            ]);

            foreach ($newMembers as $newMember) {
                if (!isset($values_per_type[$valueType])) {
                    $values_per_type["newMembers"][$valueType] = [];
                }
                for ($i = 1; $i <= 12; $i++) {
                    $method = "getM$i";
                    $values_per_type["newMembers"][$valueType]["m$i"] = $newMember->$method();
                }
            }
            foreach ($retentions as $retention) {
                if (!isset($values_per_type[$valueType])) {
                    $values_per_type["retentions"][$valueType] = [];
                }
                for ($i = 1; $i <= 12; $i++) {
                    $method = "getM$i";
                    $values_per_type["retentions"][$valueType]["m$i"] = $retention->$method();
                }
            }

            $new_members_consumptive = $this->newMembersRepository->findBy([
                "rana" => $rana,
                "valueType" => "CONS"
            ]);
            $retentions_consumptive = $this->retentionsRepository->findBy([
                "rana" => $rana,
                "valueType" => "CONS"
            ]);

            foreach ($new_members_consumptive as $c) {
                for ($i = 1; $i <= 12; $i++) {
                    $method = "getM$i";
                    $val = $c->$method() !== null ? $c->$method() : null;
                    $values_per_type["newMembers"]["CONS"]["m$i"] = $val;
                }
            }
            foreach ($retentions_consumptive as $c) {
                for ($i = 1; $i <= 12; $i++) {
                    $method = "getM$i";
                    $val = $c->$method() !== null ? $c->$method() : null;
                    $values_per_type["retentions"]["CONS"]["m$i"] = $val;
                }
            }
            $allDetails[] = array_merge($this->format($rana), [
                'newMembers'     => $values_per_type["newMembers"],
                'renewedMembers' => $renewedMembersValues,
                'retentions'     => $values_per_type["retentions"],
                'timeslot'       => $timeslot,
                'state'          => $state
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
