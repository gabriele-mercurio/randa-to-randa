<?php

namespace App\Repository;

use App\Entity\Rana;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Rana|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rana|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rana[]    findAll()
 * @method Rana[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RanaRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Rana::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param Rana $rana
     */
    public function delete(Rana $rana): void
    {
        $this->entityManager->remove($rana);
        $this->entityManager->flush();
    }

    /**
     * @param Rana $rana
     */
    public function save(Rana $rana): void
    {
        $this->entityManager->persist($rana);
        $this->entityManager->flush();
    }
}
