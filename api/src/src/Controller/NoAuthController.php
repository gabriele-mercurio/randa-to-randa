<?php

namespace App\Controller;

use Exception;
use App\Util\Util;
use App\Entity\Rana;
use App\Entity\Randa;
use Twig\Environment;
use App\Entity\Region;
use App\Util\Constants;
use App\Entity\RanaLifecycle;
use Swagger\Annotations as SWG;
use App\Formatter\RandaFormatter;
use Symfony\Component\Mime\Email;
use App\Repository\RanaRepository;
use App\Repository\UserRepository;
use App\Repository\RandaRepository;
use App\Repository\ChapterRepository;
use App\Repository\DirectorRepository;
use App\Repository\NewMemberRepository;
use App\Repository\RetentionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RegionRepository;
use App\Repository\RanaLifecycleRepository;
use App\Repository\RenewedMemberRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NoAuthController extends AbstractController
{
    /** @var DirectorRepository */
    private $directorRepository;

    /** @var NewMemberRepository */
    private $newMemberRepository;

    /** @var RandaFormatter */
    private $randaFormatter;

    /** @var RandaRepository */
    private $randaRepository;

    /** @var RanaRepository */
    private $ranaRepository;


    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var MailerInterface */
    private $mailer;

    /** @var RenewedMemberRepository */
    private $renewedMemberRepository;

    /** @var RetentionRepository */
    private $retentionRepository;

    /** @var UserRepository */
    private $userRepository;


    /** @var ChapterRepository */
    private $chapterRepository;

    /** @var RanaLifecycleRepository */
    private $ranaLifecycleRepository;

    /** @var RegionRepository */
    private $regionRepository;

    /** @var Environment */
    private $twig;


    public function __construct(
        DirectorRepository $directorRepository,
        NewMemberRepository $newMemberRepository,
        RandaFormatter $randaFormatter,
        RandaRepository $randaRepository,
        RenewedMemberRepository $renewedMemberRepository,
        RetentionRepository $retentionRepository,
        UserRepository $userRepository,
        RanaLifecycleRepository $ranaLifecycleRepository,
        ChapterRepository $chapterRepository,
        RanaRepository $ranaRepository,
        EntityManagerInterface $entityManager,
        RegionRepository $regionRepository,
        Environment $twig,
        MailerInterface $mailer
    ) {
        $this->directorRepository = $directorRepository;
        $this->newMemberRepository = $newMemberRepository;
        $this->randaFormatter = $randaFormatter;
        $this->randaRepository = $randaRepository;
        $this->renewedMemberRepository = $renewedMemberRepository;
        $this->retentionRepository = $retentionRepository;
        $this->userRepository = $userRepository;
        $this->ranaLifecycleRepository = $ranaLifecycleRepository;
        $this->chapterRepository = $chapterRepository;
        $this->ranaRepository = $ranaRepository;
        $this->entityManager = $entityManager;
        $this->regionRepository = $regionRepository;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

     /**
     *
     * @Route(path="/resetPassword", name="reset_password", methods={"POST"})
     *
     */
    public function resetPassword(Request $request): Response
    {

        file_put_contents("log", "uno\n", FILE_APPEND);
        $request = Util::normalizeRequest($request);
        $email = $request->get("email");
        header("email: " . $email);
        $user = $this->userRepository->findOneBy([
            "email" => $email
        ]);
        file_put_contents("log", "due\n", FILE_APPEND);

        $tempPassword = Util::generatePassword();
        $user->securePassword($tempPassword);

        header("tempPassword: " . $user->getFullName());
        file_put_contents("log", "ter\n", FILE_APPEND);

        $data = [
            "fullName" => $user->getFullName(),
            "tempPassword" => $tempPassword
        ];

        $email = (new TemplatedEmail())
            ->from('rosbi@studio-mercurio.it')
            ->to($email)
            ->subject("Recupero password")
            ->htmlTemplate('emails/password-recovery/html.twig')
            ->context($data);

        $this->mailer->send($email);

        file_put_contents("log", "quattro\n", FILE_APPEND);
        $response = null;
        if(!$user) {
            $response =  new JsonResponse(["message" => "Email not found"], Response::HTTP_BAD_REQUEST);
        }

        file_put_contents("log", "cinque\n", FILE_APPEND);

        $response = new JsonResponse(true, Response::HTTP_OK);
		return $response;
    }

}
