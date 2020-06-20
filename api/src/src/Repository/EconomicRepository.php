<?php

namespace App\Repository;

use App\Entity\Economic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Economic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Economic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Economic[]    findAll()
 * @method Economic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EconomicRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Economic::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param Economic $economic
     */
    public function delete(Economic $economic): void
    {
        $this->entityManager->remove($economic);
        $this->entityManager->flush();
    }

    /**
     * @param Economic $economic
     */
    public function save(Economic $economic): void
    {
        $this->entityManager->persist($economic);
        $this->entityManager->flush();
    }
}
