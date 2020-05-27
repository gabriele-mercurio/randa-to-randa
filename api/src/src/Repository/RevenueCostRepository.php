<?php

namespace App\Repository;

use App\Entity\RevenueCost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method RevenueCost|null find($id, $lockMode = null, $lockVersion = null)
 * @method RevenueCost|null findOneBy(array $criteria, array $orderBy = null)
 * @method RevenueCost[]    findAll()
 * @method RevenueCost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RevenueCostRepository extends ServiceEntityRepository
{
    public const REVENUE_COST_TYPE = [];

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, RevenueCost::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param RevenueCost $revenueCost
     */
    public function delete(RevenueCost $revenueCost): void
    {
        $this->entityManager->remove($revenueCost);
        $this->entityManager->flush();
    }

    /**
     * @param RevenueCost $revenueCost
     */
    public function save(RevenueCost $revenueCost): void
    {
        $this->entityManager->persist($revenueCost);
        $this->entityManager->flush();
    }
}
