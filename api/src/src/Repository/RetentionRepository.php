<?php

namespace App\Repository;

use App\Entity\Retention;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Retention|null find($id, $lockMode = null, $lockVersion = null)
 * @method Retention|null findOneBy(array $criteria, array $orderBy = null)
 * @method Retention[]    findAll()
 * @method Retention[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RetentionRepository extends ServiceEntityRepository
{
    public const RETENTION_VALUE_TYPE = [];

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Retention::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param Retention $retention
     */
    public function delete(Retention $retention): void
    {
        $this->entityManager->remove($retention);
        $this->entityManager->flush();
    }

    /**
     * @param Retention $retention
     */
    public function save(Retention $retention): void
    {
        $this->entityManager->persist($retention);
        $this->entityManager->flush();
    }
}
