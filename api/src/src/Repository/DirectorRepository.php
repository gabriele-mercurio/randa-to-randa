<?php

namespace App\Repository;

use App\Entity\Director;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Director|null find($id, $lockMode = null, $lockVersion = null)
 * @method Director|null findOneBy(array $criteria, array $orderBy = null)
 * @method Director[]    findAll()
 * @method Director[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirectorRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Director::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param Director $director
     */
    public function delete(Director $director): void
    {
        $this->entityManager->remove($director);
        $this->entityManager->flush();
    }

    /**
     * @param Director $director
     */
    public function save(Director $director): void
    {
        $this->entityManager->persist($director);
        $this->entityManager->flush();
    }
}
