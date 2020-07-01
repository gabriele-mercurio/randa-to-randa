<?php

namespace App\Repository;

use App\Entity\Rana;
use App\Util\Constants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Rana|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rana|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rana[]    findAll()
 * @method Rana[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RanaRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var NewMemberRepository */
    protected $newMemberRepository;

    /** @var RandaRepository */
    protected $randaRepository;

    /** @var RenewedMemberRepository */
    protected $renewedMemberRepository;

    /** @var RetentionRepository */
    protected $retentionRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerRegistry $registry,
        NewMemberRepository $newMemberRepository,
        RandaRepository $randaRepository,
        RenewedMemberRepository $renewedMemberRepository,
        RetentionRepository $retentionRepository
    ) {
        parent::__construct($registry, Rana::class);
        $this->entityManager = $entityManager;
        $this->newMemberRepository = $newMemberRepository;
        $this->randaRepository = $randaRepository;
        $this->renewedMemberRepository = $renewedMemberRepository;
        $this->retentionRepository = $retentionRepository;
    }

    /**
     * @param Rana $rana
     */
    public function delete(Rana $rana): void
    {
        $this->entityManager->remove($rana);
        $this->entityManager->flush();
    }

    /**
     * Returns na array of all the three types of members dived by month
     *
     * @param Rana $rana
     * @param string $valueType
     * @param string $timeslot
     *
     * @return array
     */
    public function getMembersQuantities(Rana $rana, string $valueType, string $timeslot): array
    {
        $response = [];
        if ($valueType == Constants::VALUE_TYPE_CONSUMPTIVE) {
            switch ($timeslot) {
                case Constants::TIMESLOT_T4:
                    $previous = $this->newMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T3
                    ]);
                    $response['newMember']['m12'] = $previous ? $previous->getM12() : 0;
                    $response['newMember']['m11'] = $previous ? $previous->getM11() : 0;
                    $response['newMember']['m10'] = $previous ? $previous->getM10() : 0;

                    $previous = $this->retentionRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T3
                    ]);
                    $response['retentionMember']['m12'] = $previous ? $previous->getM12() : 0;
                    $response['retentionMember']['m11'] = $previous ? $previous->getM11() : 0;
                    $response['retentionMember']['m10'] = $previous ? $previous->getM10() : 0;

                    $previous = $this->renewedMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T3
                    ]);
                    $response['renewedMember']['m12'] = ($previous ? $previous->getM12() : 0) - $response['retentionMember']['m12'] + $response['newMember']['m12'];
                    $response['renewedMember']['m11'] = ($previous ? $previous->getM11() : 0) - $response['retentionMember']['m11'] + $response['newMember']['m11'];
                    $response['renewedMember']['m10'] = ($previous ? $previous->getM10() : 0) - $response['retentionMember']['m10'] + $response['newMember']['m10'];
                case Constants::TIMESLOT_T3:
                    $previous = $this->newMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T2
                    ]);
                    $response['newMember']['m9'] = $previous ? $previous->getM9() : 0;
                    $response['newMember']['m8'] = $previous ? $previous->getM8() : 0;
                    $response['newMember']['m7'] = $previous ? $previous->getM7() : 0;

                    $previous = $this->retentionRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T2
                    ]);
                    $response['retentionMember']['m9'] = $previous ? $previous->getM9() : 0;
                    $response['retentionMember']['m8'] = $previous ? $previous->getM8() : 0;
                    $response['retentionMember']['m7'] = $previous ? $previous->getM7() : 0;

                    $previous = $this->renewedMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T2
                    ]);
                    $response['renewedMember']['m9'] = ($previous ? $previous->getM9() : 0) - $response['retentionMember']['m9'] + $response['newMember']['m9'];
                    $response['renewedMember']['m8'] = ($previous ? $previous->getM8() : 0) - $response['retentionMember']['m8'] + $response['newMember']['m8'];
                    $response['renewedMember']['m7'] = ($previous ? $previous->getM7() : 0) - $response['retentionMember']['m7'] + $response['newMember']['m7'];
                case Constants::TIMESLOT_T2:
                    $previous = $this->newMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T1
                    ]);
                    $response['newMember']['m6'] = $previous ? $previous->getM6() : 0;
                    $response['newMember']['m5'] = $previous ? $previous->getM5() : 0;
                    $response['newMember']['m4'] = $previous ? $previous->getM4() : 0;

                    $previous = $this->retentionRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T1
                    ]);
                    $response['retentionMember']['m6'] = $previous ? $previous->getM6() : 0;
                    $response['retentionMember']['m5'] = $previous ? $previous->getM5() : 0;
                    $response['retentionMember']['m4'] = $previous ? $previous->getM4() : 0;

                    $previous = $this->renewedMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T1
                    ]);
                    $response['renewedMember']['m6'] = ($previous ? $previous->getM6() : 0) - $response['retentionMember']['m6'] + $response['newMember']['m6'];
                    $response['renewedMember']['m5'] = ($previous ? $previous->getM5() : 0) - $response['retentionMember']['m5'] + $response['newMember']['m5'];
                    $response['renewedMember']['m4'] = ($previous ? $previous->getM4() : 0) - $response['retentionMember']['m4'] + $response['newMember']['m4'];
                case Constants::TIMESLOT_T1:
                    $response['newMember']['m3'] = 0;
                    $response['newMember']['m2'] = 0;
                    $response['newMember']['m1'] = 0;

                    $response['retentionMember']['m3'] = 0;
                    $response['retentionMember']['m2'] = 0;
                    $response['retentionMember']['m1'] = 0;

                    $lastYear = ((int) date("Y")) - 1;
                    $randa = $this->randaRepository->findOneBy([
                        'region' => $rana->getChapter()->getRegion(),
                        'year' => $lastYear
                    ]);

                    if (!is_null($randa)) {
                        $previousRana = $this->findOneBy([
                            'chapter' => $rana->getChapter(),
                            'randa' => $randa
                        ]);

                        if (!is_null($previousRana)) {
                            $previous = $this->renewedMemberRepository->findOneBy([
                                'rana' => $previousRana,
                                'valueType' => $valueType,
                                'timeslot' => Constants::TIMESLOT_T4
                            ]);
                            $response['renewedMember']['m3'] = ($previous ? $previous->getM3() : 0) - $response['retentionMember']['m3'] + $response['newMember']['m3'];
                            $response['renewedMember']['m2'] = ($previous ? $previous->getM2() : 0) - $response['retentionMember']['m2'] + $response['newMember']['m2'];
                            $response['renewedMember']['m1'] = ($previous ? $previous->getM1() : 0) - $response['retentionMember']['m1'] + $response['newMember']['m1'];
                        } else {
                            $response['renewedMember']['m3'] = $response['retentionMember']['m3'] + $response['newMember']['m3'];
                            $response['renewedMember']['m2'] = $response['retentionMember']['m2'] + $response['newMember']['m2'];
                            $response['renewedMember']['m1'] = $response['retentionMember']['m1'] + $response['newMember']['m1'];
                        }
                    } else {
                        $response['renewedMember']['m3'] = $response['retentionMember']['m3'] + $response['newMember']['m3'];
                        $response['renewedMember']['m2'] = $response['retentionMember']['m2'] + $response['newMember']['m2'];
                        $response['renewedMember']['m1'] = $response['retentionMember']['m1'] + $response['newMember']['m1'];
                    }
            }
        } else {
            switch ($timeslot) {
                case Constants::TIMESLOT_T0:
                    $response['newMember']['m1'] = 0;
                    $response['newMember']['m2'] = 0;
                    $response['newMember']['m3'] = 0;

                    $response['retentionMember']['m1'] = 0;
                    $response['retentionMember']['m2'] = 0;
                    $response['retentionMember']['m3'] = 0;

                    $lastYear = ((int) date("Y")) - 1;
                    $randa = $this->randaRepository->findOneBy([
                        'region' => $rana->getChapter()->getRegion(),
                        'year' => $lastYear
                    ]);

                    if (!is_null($randa)) {
                        $previousRana = $this->findOneBy([
                            'chapter' => $rana->getChapter(),
                            'randa' => $randa
                        ]);

                        if (!is_null($previousRana)) {
                            $previous = $this->renewedMemberRepository->findOneBy([
                                'rana' => $previousRana,
                                'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE,
                                'timeslot' => Constants::TIMESLOT_T4
                            ]);
                            $response['renewedMember']['m1'] = ($previous ? $previous->getM1() : 0) - $response['retentionMember']['m1'] + $response['newMember']['m1'];
                            $response['renewedMember']['m2'] = ($previous ? $previous->getM2() : 0) - $response['retentionMember']['m2'] + $response['newMember']['m2'];
                            $response['renewedMember']['m3'] = ($previous ? $previous->getM3() : 0) - $response['retentionMember']['m3'] + $response['newMember']['m3'];
                        } else {
                            $response['renewedMember']['m1'] = $response['retentionMember']['m1'] + $response['newMember']['m1'];
                            $response['renewedMember']['m2'] = $response['retentionMember']['m2'] + $response['newMember']['m2'];
                            $response['renewedMember']['m3'] = $response['retentionMember']['m3'] + $response['newMember']['m3'];
                        }
                    } else {
                        $response['renewedMember']['m1'] = $response['retentionMember']['m1'] + $response['newMember']['m1'];
                        $response['renewedMember']['m2'] = $response['retentionMember']['m2'] + $response['newMember']['m2'];
                        $response['renewedMember']['m3'] = $response['retentionMember']['m3'] + $response['newMember']['m3'];
                    }
                case Constants::TIMESLOT_T1:
                    $previous = $this->newMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE,
                        'timeslot' => Constants::TIMESLOT_T0
                    ]) ?? $this->newMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T0
                    ]);
                    $response['newMember']['m4'] = $previous ? $previous->getM4() : 0;
                    $response['newMember']['m5'] = $previous ? $previous->getM5() : 0;
                    $response['newMember']['m6'] = $previous ? $previous->getM6() : 0;

                    $previous = $this->retentionRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE,
                        'timeslot' => Constants::TIMESLOT_T0
                    ]) ?? $this->retentionRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T0
                    ]);
                    $response['retentionMember']['m4'] = $previous ? $previous->getM4() : 0;
                    $response['retentionMember']['m5'] = $previous ? $previous->getM5() : 0;
                    $response['retentionMember']['m6'] = $previous ? $previous->getM6() : 0;

                    $previous = $this->renewedMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE,
                        'timeslot' => Constants::TIMESLOT_T0
                    ]) ?? $this->renewedMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T0
                    ]);
                    $response['renewedMember']['m4'] = $previous ? $previous->getM4() : 0;
                    $response['renewedMember']['m5'] = $previous ? $previous->getM5() : 0;
                    $response['renewedMember']['m6'] = $previous ? $previous->getM6() : 0;
                case Constants::TIMESLOT_T2:
                    $previous = $this->newMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE,
                        'timeslot' => Constants::TIMESLOT_T1
                    ]) ?? $this->newMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T1
                    ]);
                    $response['newMember']['m7'] = $previous ? $previous->getM7() : 0;
                    $response['newMember']['m8'] = $previous ? $previous->getM8() : 0;
                    $response['newMember']['m9'] = $previous ? $previous->getM9() : 0;

                    $previous = $this->retentionRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE,
                        'timeslot' => Constants::TIMESLOT_T1
                    ]) ?? $this->retentionRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T1
                    ]);
                    $response['retentionMember']['m7'] = $previous ? $previous->getM7() : 0;
                    $response['retentionMember']['m8'] = $previous ? $previous->getM8() : 0;
                    $response['retentionMember']['m9'] = $previous ? $previous->getM9() : 0;

                    $previous = $this->renewedMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE,
                        'timeslot' => Constants::TIMESLOT_T1
                    ]) ?? $this->renewedMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T1
                    ]);
                    $response['renewedMember']['m7'] = $previous ? $previous->getM7() : 0;
                    $response['renewedMember']['m8'] = $previous ? $previous->getM8() : 0;
                    $response['renewedMember']['m9'] = $previous ? $previous->getM9() : 0;
                case Constants::TIMESLOT_T3:
                    $previous = $this->newMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE,
                        'timeslot' => Constants::TIMESLOT_T2
                    ]) ?? $this->newMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T2
                    ]);
                    $response['newMember']['m10'] = $previous ? $previous->getM10() : 0;
                    $response['newMember']['m11'] = $previous ? $previous->getM11() : 0;
                    $response['newMember']['m12'] = $previous ? $previous->getM12() : 0;

                    $previous = $this->retentionRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE,
                        'timeslot' => Constants::TIMESLOT_T2
                    ]) ?? $this->retentionRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T2
                    ]);
                    $response['retentionMember']['m10'] = $previous ? $previous->getM10() : 0;
                    $response['retentionMember']['m11'] = $previous ? $previous->getM11() : 0;
                    $response['retentionMember']['m12'] = $previous ? $previous->getM12() : 0;

                    $previous = $this->renewedMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE,
                        'timeslot' => Constants::TIMESLOT_T2
                    ]) ?? $this->renewedMemberRepository->findOneBy([
                        'rana' => $rana,
                        'valueType' => $valueType,
                        'timeslot' => Constants::TIMESLOT_T2
                    ]);
                    $response['renewedMember']['m10'] = $previous ? $previous->getM10() : 0;
                    $response['renewedMember']['m11'] = $previous ? $previous->getM11() : 0;
                    $response['renewedMember']['m12'] = $previous ? $previous->getM12() : 0;
            }
        }

        return $response;
    }

    /**
     * @param Rana $rana
     */
    public function save(Rana $rana): void
    {
        $this->entityManager->persist($rana);
        $this->entityManager->flush();
    }
}
