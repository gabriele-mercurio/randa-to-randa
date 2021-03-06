<?php

namespace App\Repository;

use App\Entity\Randa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Randa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Randa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Randa[]    findAll()
 * @method Randa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RandaRepository extends ServiceEntityRepository
{
    public const RANDA_CURRENT_TIMESLOT_T0 = 'T0';
    public const RANDA_CURRENT_TIMESLOT_T1 = 'T1';
    public const RANDA_CURRENT_TIMESLOT_T2 = 'T2';
    public const RANDA_CURRENT_TIMESLOT_T3 = 'T3';
    public const RANDA_CURRENT_TIMESLOT_T4 = 'T4';

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Randa::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param Randa $randa
     */
    public function delete(Randa $randa): void
    {
        $this->entityManager->remove($randa);
        $this->entityManager->flush();
    }

    /**
     * @param Randa $randa
     */
    public function save(Randa $randa): void
    {
        $this->entityManager->persist($randa);
        $this->entityManager->flush();
    }
}
