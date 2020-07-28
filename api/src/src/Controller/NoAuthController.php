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
use App\Entity\Members;
use App\Entity\States;
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
use App\Repository\MembersRepository;
use App\Repository\StatesRepository;
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

    /** @var MembersRepository */
    private $membersRepository;

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
        MailerInterface $mailer,
        MembersRepository $membersRepository,
        StatesRepository $statesRepository
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
        $this->membersRepository = $membersRepository;
        $this->statesRepository = $statesRepository;
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
        $this->userRepository->save($user);

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
        if (!$user) {
            $response =  new JsonResponse(["message" => "Email not found"], Response::HTTP_BAD_REQUEST);
        }

        file_put_contents("log", "cinque\n", FILE_APPEND);

        $response = new JsonResponse(true, Response::HTTP_OK);
        return $response;
    }

    /**
     * Importer for chapters from the old DB
     *
     * @Route(path="/removeTestChapters", name="removeTestChapters", methods={"GET"})
     *
     */
    public function removeTestChapters(): Response
    {

        $region = $this->regionRepository->find("015cb62d-5136-4eef-b24e-1b6891a21cd2");
        $chapters = $this->chapterRepository->findBy([
            "region" => $region
        ]);

        $log = [];

        foreach ($chapters as $chapter) {

            $ranas = $this->ranaRepository->findBy([
                "chapter" => $chapter

            ]);

            foreach ($ranas as $rana) {
                $rana_lifecycle = $this->ranaLifecycleRepository->findBy([
                    "rana" => $rana
                ]);
                foreach ($rana_lifecycle as $rlc) {
                    $this->entityManager->remove($rlc);
                    $this->entityManager->flush();
                }


                $retentions = $this->retentionRepository->findBy([
                    "rana" => $rana
                ]);
                foreach ($retentions as $r) {
                    $this->entityManager->remove($r);
                    $this->entityManager->flush();
                }

                $new_members = $this->newMemberRepository->findBy([
                    "rana" => $rana
                ]);
                foreach ($new_members as $nm) {
                    $this->entityManager->remove($nm);
                    $this->entityManager->flush();
                }

                try {
                    $log[] = $rana->getId();
                    $this->entityManager->remove($rana);
                    $this->entityManager->flush();
                } catch (Exception $e) {
                    return new JsonResponse($e->getMessage());
                }


                $this->entityManager->remove($chapter);
                $this->entityManager->flush();
            }
        }


        return new JsonResponse($log);
    }


    /**
     * Importer for chapters from the old DB
     *
     * @Route(path="/fillMembersTable", name="fillMembersTable", methods={"GET"})
     *
     */
    public function fillMembersTable(): Response
    {

        $chapters = $this->chapterRepository->findAll();
        $chapters_values = [];
        foreach ($chapters as $chapter) {

            $new_members_values = [];
            $ret_members_values = [];
            $act_members_values = [];

            $initialMembers = $chapter->getMembers();
            $randa = $this->randaRepository->findOneBy([
                "region" => $chapter->getRegion(),
                "year" => 2020
            ]);
            $rana = $this->ranaRepository->findOneBy([
                "randa" => $randa,
                "chapter" => $chapter
            ]);
            $new_members_cons = $this->newMemberRepository->findOneBy([
                "rana" => $rana,
                "valueType" => "CONS"
            ]);
            $new_members = $this->newMemberRepository->findOneBy([
                "rana" => $rana,
                "timeslot" => $randa->getCurrentTimeslot(),
                "valueType" => "APPR"
            ]);

            $ret_members_cons = $this->retentionRepository->findOneBy([
                "rana" => $rana,
                "valueType" => "CONS"
            ]);

            $ret_members = $this->retentionRepository->findOneBy([
                "rana" => $rana,
                "timeslot" => $randa->getCurrentTimeslot(),
                "valueType" => "APPR"
            ]);

            if ($chapter->getName() == 'BNI Andromeda') {
                return new JsonResponse($randa->getCurrentTimeslot());
            }


            $values = "";
            $members = new Members();
            $members->setChapter($chapter);
            $members->setYear(2020);

            for ($i = 0; $i < 12; $i++) {
                $method = "getM" . ($i + 1);
                if ($i == 0) {
                    $act_members_values[0] = $initialMembers;
                } else {
                    $new_value = ($new_members_cons && $new_members_cons->$method() !== null) ? $new_members_cons->$method() : ($new_members && $new_members->$method() !== null ? $new_members->$method() : 0);
                    $ret_value = ($ret_members_cons && $ret_members_cons->$method() !== null) ? $ret_members_cons->$method() : ($ret_members && $ret_members->$method() !== null ? $ret_members->$method() : 0);
                    $act_members_values[$i] = $act_members_values[$i - 1] + ($new_value - $ret_value);
                    $members->setMonth($i + 1, $act_members_values[$i - 1] + ($new_value - $ret_value));
                }
            }
            $this->membersRepository->save($members);
        }


        return new JsonResponse($chapters_values);
    }


    /**
     * Importer for chapters from the old DB
     *
     * @Route(path="/fillStatesTable", name="fillStatesTable", methods={"GET"})
     *
     */
    public function fillStatesTable(): Response
    {
        $chapters = $this->chapterRepository->findAll();
        $currentYear = (int) date("Y");
        $chapters_values = [];

        foreach ($chapters as $chapter) {

            $states = new States();
            $states->setChapter($chapter);
            $states->setYear($currentYear);

            $chapter_data = [];
            $chapter_data["name"] = $chapter->getName();
            $chapter_data["values"] = [];
            $launch_core_group = $chapter->getActualLaunchCoregroupDate() ?? $chapter->getPrevLaunchCoregroupDate();
            $launch_chapter = $chapter->getActualLaunchChapterDate() ?? $chapter->getPrevLaunchChapterDate();

            if ($launch_core_group) {
                $year_cg = $launch_core_group->format("Y");
                $m_cg = $year_cg > $currentYear ? 13 : ($year_cg < $currentYear ? 0 : $launch_core_group->format("m"));
            } else {
                $m_cg = 13;
            }

            if ($launch_chapter) {
                $year_c = $launch_chapter->format("Y");
                $m_c =  $year_c > $currentYear ? 13 : ($year_c < $currentYear ? 0 : $launch_chapter->format("m"));
            } else {
                $m_c = 13;
            }

            for ($i = 1; $i <= 12; $i++) {
                if($i < $m_c) {
                    if($i < $m_cg) {
                        $chapter_data["values"][$i] = "PROJECT";
                        $states->setMonth($i, "PROJECT");
                    } else {
                        $chapter_data["values"][$i] = "CORE_GROUP";
                        $states->setMonth($i, "CORE_GROUP");
                    }
                } else {
                    $chapter_data["values"][$i] = "CHAPTER";
                    $states->setMonth($i, "CHAPTER");
                }
            }

            $this->entityManager->persist($states);
            $this->entityManager->flush();

            $chapters_values[] = $chapter_data;
        }
        return new JsonResponse($chapters_values);
    }
}
