<?php

namespace App\Repository;

use App\Entity\Region;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Environment */
    private $twig;

    /** @var Swift_Mailer */
    private $mailer;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        Environment $twig,
        ManagerRegistry $registry,
        Swift_Mailer $mailer,
        TranslatorInterface $translator
    ) {
        parent::__construct($registry, User::class);
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->twig = $twig;
    }

    /**
     * If is null $actAs, returns $user as user and response 200 OK as code otherwise if $user
     * is admin and $actAs is a valid user id, returns $actAs as user and response 200 OK as code.
     * If $user is not admin or $actAs is not a valid user, returns $user as user and respectively
     * response 403 FORBIDDEN or 404 NOT FOUND as code.
     *
     * @param User $user
     * @param string|null $actAs
     *
     * @return array
     */
    public function checkUser(User $user, ?string $actAs): array
    {
        $response = [
            'code' => Response::HTTP_OK,
            'user' => $user
        ];

        if (!is_null($actAs)) {
            if (!$user->isAdmin()) {
                $response['code'] = Response::HTTP_FORBIDDEN;
            } else {
                $actAs = $this->find($actAs);
                if (is_null($actAs)) {
                    $response['code'] = Response::HTTP_NOT_FOUND;
                } else {
                    $response['user'] = $actAs;
                }
            }
        }

        return $response;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function getUserByEmail(string $email): User
    {
        return $this->findOneBy([
            "email" => $email
        ]);
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->findAll();
    }

    /**
     * @return User[]
     */
    public function getUsersPerRegion(Region $region): array
    {
        $qb = $this->createQueryBuilder('u');
        return $qb->join('u.directors', 'd')
            ->where('d.region = :dregion_id')
            ->setParameter('dregion_id', $region->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     */
    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function sendNewUserEmail(User $user, string $tempPasswd)
    {
        $email = $this->mailer->createMessage();

        $data = [
            'tempPasswd' => $tempPasswd,
            'title' => $this->translator->trans('email.newUser.title'),
            'user' => $user
        ];

        $email
            ->setFrom($_ENV['MAIL_NO_REPLY_ADDRESS'], $_ENV['MAIL_SENDER_NAME'])
            ->addTo($user->getEmail(), $user->getFullName())
            ->setSubject($data['title'])
            ->setBody($this->twig->render("emails/new-user/html.twig", $data), "text/html")
            ->addPart($this->twig->render("emails/new-user/txt.twig", $data), "text/plain");

        return $this->mailer->send($email);
    }
}
