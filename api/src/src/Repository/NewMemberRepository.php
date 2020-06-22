<?php

namespace App\Repository;

use App\Entity\NewMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method NewMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewMember[]    findAll()
 * @method NewMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewMemberRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, NewMember::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param NewMember $newMember
     */
    public function delete(NewMember $newMember): void
    {
        $this->entityManager->remove($newMember);
        $this->entityManager->flush();
    }

    /**
     * @param NewMember $newMember
     */
    public function save(NewMember $newMember): void
    {
        $this->entityManager->persist($newMember);
        $this->entityManager->flush();
    }
}
