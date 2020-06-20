<?php

namespace App\Repository;

use App\Entity\TrafficLight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method TrafficLight|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrafficLight|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrafficLight[]    findAll()
 * @method TrafficLight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrafficLightRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, TrafficLight::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param TrafficLight $trafficLight
     */
    public function delete(TrafficLight $trafficLight): void
    {
        $this->entityManager->remove($trafficLight);
        $this->entityManager->flush();
    }

    /**
     * @param TrafficLight $trafficLight
     */
    public function save(TrafficLight $trafficLight): void
    {
        $this->entityManager->persist($trafficLight);
        $this->entityManager->flush();
    }
}
