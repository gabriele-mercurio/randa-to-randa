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
