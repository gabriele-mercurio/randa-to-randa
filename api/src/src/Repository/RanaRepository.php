<?php

namespace App\Repository;

use App\Entity\Rana;
use App\Entity\RenewedMember;
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

    function getPreviousRenewedMembers(Rana $rana, string $valueType, RandaRepository $randaRepository, RanaRepository $ranaRepository, RenewedMemberRepository $renewedMemberRepository): ?RenewedMember
    {
        $previousRenewedMembers = null;
        $lastYear = ((int) date("Y")) - 1;
        $randa = $randaRepository->findOneBy([
            'region' => $rana->getChapter()->getRegion(),
            'year' => $lastYear
        ]);

        if (!is_null($randa)) {
            $previousRana = $ranaRepository->findOneBy([
                'chapter' => $rana->getChapter(),
                'randa' => $randa
            ]);

            if (!is_null($previousRana)) {
                $previousRenewedMembers = $renewedMemberRepository->findOneBy([
                    'rana' => $previousRana,
                    'valueType' => $valueType,
                    'timeslot' => Constants::TIMESLOT_T4
                ]);
            }
        }

        return $previousRenewedMembers;
    }

    private function calculateMembers(Rana $rana, string $valueType, int $slot): array
    {


        switch ($slot) {
            case 0:
                $months = ["m1", "m2", "m3"];
            case 1:
                $months = $valueType == Constants::VALUE_TYPE_CONSUMPTIVE ? ["m1", "m2", "m3"] : ["m4", "m5", "m6"];
                break;
            case 2:
                $months = $valueType == Constants::VALUE_TYPE_CONSUMPTIVE ? ["m4", "m5", "m6"] : ["m7", "m8", "m9"];
                break;
            case 3:
                $months = $valueType == Constants::VALUE_TYPE_CONSUMPTIVE ? ["m7", "m8", "m9"] : ["m10", "m11", "m12"];
                break;
            case 4:
                $months = ["m10", "m11", "m12"];
                break;
        }
        $response = [];

        if ($valueType == Constants::VALUE_TYPE_CONSUMPTIVE && $slot > 1) {
            $timeslot = $slot - 1;
            $previousNewMembers = $this->newMemberRepository->findOneBy([
                'rana' => $rana,
                'valueType' => $valueType,
                'timeslot' => "T$timeslot"
            ]);
            $previousRenewedMembers = $this->renewedMemberRepository->findOneBy([
                'rana' => $rana,
                'valueType' => $valueType,
                'timeslot' => "T$timeslot"
            ]);
            $previousRetentionMembers = $this->retentionRepository->findOneBy([
                'rana' => $rana,
                'valueType' => $valueType,
                'timeslot' => "T$timeslot"
            ]);

            foreach ($months as $month) {
                $method = "get" . strtoupper($month);
                $response['newMember'][$month] = $previousNewMembers ? $previousNewMembers->$method() : 0;
                $response['renewedMember'][$month] = $previousRenewedMembers ? $previousRenewedMembers->$method() : 0;
                $response['retentionMember'][$month] = $previousRetentionMembers ? $previousRetentionMembers->$method() : 0;
            }
        } elseif ($valueType != Constants::VALUE_TYPE_CONSUMPTIVE && $slot) {
            $timeslot = $slot - 1;
            $previousNewMembers = $this->newMemberRepository->findOneBy([
                'rana' => $rana,
                'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE,
                'timeslot' => "T$timeslot"
            ]) ?? $this->newMemberRepository->findOneBy([
                'rana' => $rana,
                'valueType' => $valueType,
                'timeslot' => "T$timeslot"
            ]);
            $previousRenewedMembers = $this->renewedMemberRepository->findOneBy([
                'rana' => $rana,
                'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE,
                'timeslot' => "T$timeslot"
            ]) ?? $this->renewedMemberRepository->findOneBy([
                'rana' => $rana,
                'valueType' => $valueType,
                'timeslot' => "T$timeslot"
            ]);
            $previousRetentionMembers = $this->retentionRepository->findOneBy([
                'rana' => $rana,
                'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE,
                'timeslot' => "T$timeslot"
            ]) ?? $this->retentionRepository->findOneBy([
                'rana' => $rana,
                'valueType' => $valueType,
                'timeslot' => "T$timeslot"
            ]);

            foreach ($months as $month) {
                $method = "get" . strtoupper($month);
                $response['newMember'][$month] = $previousNewMembers ? $previousNewMembers->$method() : 0;
                $response['renewedMember'][$month] = $previousRenewedMembers ? $previousRenewedMembers->$method() : 0;
                $response['retentionMember'][$month] = $previousRetentionMembers ? $previousRetentionMembers->$method() : 0;
            }
        } elseif (($valueType == Constants::VALUE_TYPE_CONSUMPTIVE && $slot == 1) || ($valueType != Constants::VALUE_TYPE_CONSUMPTIVE && !$slot)) {
            $previousRenewedMembers = $this->getPreviousRenewedMembers($rana, $valueType, $this->randaRepository, $this, $this->renewedMemberRepository);

            foreach ($months as $month) {
                $method = "get" . strtoupper($month);
                $response['newMember'][$month] = 0;
                $response['renewedMember'][$month] = $previousRenewedMembers ? $previousRenewedMembers->$method() : 0;
                $response['retentionMember'][$month] = 0;
            }
        }

        return $response;
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
        $startSlot = (int) substr($timeslot, -1);
        if ($valueType == Constants::VALUE_TYPE_CONSUMPTIVE) {
            $endSlot = 1;
        } else {
            $endSlot = 3;
        }

        for ($i = $startSlot; $startSlot > $endSlot ? $i >= $endSlot : $i <= $endSlot; $startSlot > $endSlot ? $i-- : $i++) {
            $response = array_merge($response, $this->calculateMembers($rana, $valueType, $i));
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
