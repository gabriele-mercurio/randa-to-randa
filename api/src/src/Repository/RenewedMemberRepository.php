<?php

namespace App\Repository;

use App\Entity\RenewedMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method RenewedMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method RenewedMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method RenewedMember[]    findAll()
 * @method RenewedMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RenewedMemberRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, RenewedMember::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param RenewedMember $renewedMember
     */
    public function delete(RenewedMember $renewedMember): void
    {
        $this->entityManager->remove($renewedMember);
        $this->entityManager->flush();
    }

    /**
     * @param RenewedMember $renewedMember
     */
    public function save(RenewedMember $renewedMember): void
    {
        $this->entityManager->persist($renewedMember);
        $this->entityManager->flush();
    }
}
