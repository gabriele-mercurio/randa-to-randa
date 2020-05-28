<?php

namespace App\Repository;

use App\Entity\Strategy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Strategy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Strategy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Strategy[]    findAll()
 * @method Strategy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StrategyRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Strategy::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param Strategy $strategy
     */
    public function delete(Strategy $strategy): void
    {
        $this->entityManager->remove($strategy);
        $this->entityManager->flush();
    }

    /**
     * @param Strategy $strategy
     */
    public function save(Strategy $strategy): void
    {
        $this->entityManager->persist($strategy);
        $this->entityManager->flush();
    }
}
