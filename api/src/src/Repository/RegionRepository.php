<?php

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Region|null find($id, $lockMode = null, $lockVersion = null)
 * @method Region|null findOneBy(array $criteria, array $orderBy = null)
 * @method Region[]    findAll()
 * @method Region[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegionRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Region::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param Region $region
     */
    public function delete(Region $region): void
    {
        $this->entityManager->remove($region);
        $this->entityManager->flush();
    }

    /**
     * @param Region $region
     */
    public function save(Region $region): void
    {
        $this->entityManager->persist($region);
        $this->entityManager->flush();
    }
}
