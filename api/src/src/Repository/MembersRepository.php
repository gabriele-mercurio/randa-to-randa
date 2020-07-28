<?php

namespace App\Repository;

use App\Entity\Members;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Members|null find($id, $lockMode = null, $lockVersion = null)
 * @method Members|null findOneBy(array $criteria, array $orderBy = null)
 * @method Members[]    findAll()
 * @method Members[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembersRepository extends ServiceEntityRepository
{

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Members::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param MembersRepository $membesRepository
     */
    public function delete(Members $member): void
    {
        $this->entityManager->remove($member);
        $this->entityManager->flush();
    }

    /**
     * @param Member $member
     */
    public function save(Members $member): void
    {
        $this->entityManager->persist($member);
        $this->entityManager->flush();
    }

}
