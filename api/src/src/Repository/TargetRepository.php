<?php

namespace App\Repository;

use App\Entity\Target;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Target|null find($id, $lockMode = null, $lockVersion = null)
 * @method Target|null findOneBy(array $criteria, array $orderBy = null)
 * @method Target[]    findAll()
 * @method Target[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TargetRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Target::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param Target $target
     */
    public function delete(Target $target): void
    {
        $this->entityManager->remove($target);
        $this->entityManager->flush();
    }

    /**
     * @param Target $target
     */
    public function save(Target $target): void
    {
        $this->entityManager->persist($target);
        $this->entityManager->flush();
    }
}
