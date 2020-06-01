<?php

namespace App\Repository;

use App\Entity\Director;
use App\Entity\Region;
use App\Entity\User;
use App\Util\Util;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method Director|null find($id, $lockMode = null, $lockVersion = null)
 * @method Director|null findOneBy(array $criteria, array $orderBy = null)
 * @method Director[]    findAll()
 * @method Director[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirectorRepository extends ServiceEntityRepository
{
    public const DIRECTOR_PAY_TYPE_ANNUAL = 'ANNUAL';
    public const DIRECTOR_PAY_TYPE_MONTHLY = 'MONTHLY';
    public const DIRECTOR_ROLE_AREA = 'AREA';
    public const DIRECTOR_ROLE_ASSISTANT = 'ASSISTANT';
    public const DIRECTOR_ROLE_EXECUTIVE = 'EXECUTIVE';
    public const DIRECTOR_ROLE_NATIONAL = 'NATIONAL';

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Director::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user        The logged user
     * @param Region $region    The involved region
     * @param string|null $role The requeste role to check for
     *
     * @return array            An array with an error http code (defaults to 200 OK) and the
     *                          director if found. If the user is not a director for the region
     *                          or he/she doesn't have the specified role for that region,
     *                          director will be null and the error code will be set to 403 FORBIDDEN
     */
    public function checkDirectorRole(User $user, Region $region, ?string $role = null): array
    {
        $response = [
            'code' => Response::HTTP_OK,
            'director' => null
        ];

        $params = [
            'user' => $user,
            'region' => $region
        ];

        if (!is_null($role)) {
            $params['role'] = $role;
        }

        $directors = $this->findBy($params);

        if (empty($directors)) {
            $response['code'] = Response::HTTP_FORBIDDEN;
        } else {
            if (count($directors) > 1) {
                $maxFoundedRole = $this->directorRepository::DIRECTOR_ROLE_ASSISTANT;
                foreach ($directors as $director) {
                    $role = $director->getRole();
                    if ($role == $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE) {
                        $maxFoundedRole = $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE;
                        break;
                    }
                    if ($role == $this->directorRepository::DIRECTOR_ROLE_AREA) {
                        $maxFoundedRole = $this->directorRepository::DIRECTOR_ROLE_AREA;
                    }
                }
                $directors = array_filter($directors, function ($d) use($maxFoundedRole) {
                    return $d->getRole() == $maxFoundedRole;
                });
            }

            $response['director'] = Util::arrayGetValue($directors, 0, null);
        }

        return $response;
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
