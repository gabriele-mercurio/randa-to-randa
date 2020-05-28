<?php

namespace App\Repository;

use App\Entity\Chapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Chapter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chapter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chapter[]    findAll()
 * @method Chapter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChapterRepository extends ServiceEntityRepository
{
    public const CHAPTER_CURRENT_STATE_CHAPTER = 'CHAPTER';
    public const CHAPTER_CURRENT_STATE_CORE_GROUP = 'CORE_GROUP';
    public const CHAPTER_CURRENT_STATE_PROJECT = 'PROJECT';

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Chapter::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param Chapter $chapter
     */
    public function delete(Chapter $chapter): void
    {
        $this->entityManager->remove($chapter);
        $this->entityManager->flush();
    }

    /**
     * @param Chapter $chapter
     */
    public function save(Chapter $chapter): void
    {
        $this->entityManager->persist($chapter);
        $this->entityManager->flush();
    }

    public function validateName(string $name): bool
    {
        if (!strlen($name) || strlen($name) > 32) {
            return false;
        }
        return true;
    }
}
