<?php

namespace App\Repository;

use App\Entity\StrategyPerRanda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method StrategyPerRanda|null find($id, $lockMode = null, $lockVersion = null)
 * @method StrategyPerRanda|null findOneBy(array $criteria, array $orderBy = null)
 * @method StrategyPerRanda[]    findAll()
 * @method StrategyPerRanda[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StrategyPerRandaRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, StrategyPerRanda::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param StrategyPerRanda $strategyPerRanda
     */
    public function delete(StrategyPerRanda $strategyPerRanda): void
    {
        $this->entityManager->remove($strategyPerRanda);
        $this->entityManager->flush();
    }

    /**
     * @param StrategyPerRanda $strategyPerRanda
     */
    public function save(StrategyPerRanda $strategyPerRanda): void
    {
        $this->entityManager->persist($strategyPerRanda);
        $this->entityManager->flush();
    }
}
