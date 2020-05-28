<?php

namespace App\Repository;

use App\Entity\RanaLifecycle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method RanaLifecycle|null find($id, $lockMode = null, $lockVersion = null)
 * @method RanaLifecycle|null findOneBy(array $criteria, array $orderBy = null)
 * @method RanaLifecycle[]    findAll()
 * @method RanaLifecycle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RanaLifecycleRepository extends ServiceEntityRepository
{
    public const RANA_LIFECYCLE_CURRENT_TIMESLOT_T0 = 'T0';
    public const RANA_LIFECYCLE_CURRENT_TIMESLOT_T1 = 'T1';
    public const RANA_LIFECYCLE_CURRENT_TIMESLOT_T2 = 'T2';
    public const RANA_LIFECYCLE_CURRENT_TIMESLOT_T3 = 'T3';
    public const RANA_LIFECYCLE_CURRENT_TIMESLOT_T4 = 'T4';
    public const RANA_LIFECYCLE_STATUS_APPROVED = 'APPROVED';
    public const RANA_LIFECYCLE_STATUS_PROPOSED = 'PROPOSED';
    public const RANA_LIFECYCLE_STATUS_REFUSED = 'REFUSED';
    public const RANA_LIFECYCLE_STATUS_TODO = 'TODO';

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, RanaLifecycle::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param RanaLifecycle $ranaLifecycle
     */
    public function delete(RanaLifecycle $ranaLifecycle): void
    {
        $this->entityManager->remove($ranaLifecycle);
        $this->entityManager->flush();
    }

    /**
     * @param RanaLifecycle $ranaLifecycle
     */
    public function save(RanaLifecycle $ranaLifecycle): void
    {
        $this->entityManager->persist($ranaLifecycle);
        $this->entityManager->flush();
    }
}
