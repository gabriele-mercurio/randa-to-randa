<?php

namespace App\Controller;

use Exception;
use App\Util\Util;
use App\Entity\Rana;
use App\Entity\Randa;
use App\Entity\Chapter;
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
use App\Repository\RegionRepository;
use App\Repository\ChapterRepository;
use App\Repository\DirectorRepository;
use App\Repository\NewMemberRepository;
use App\Repository\RetentionRepository;
use Doctrine\ORM\EntityManagerInterface;
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

class DashboardController extends AbstractController
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
     * Get dashboard
     *
     * @Route(path="/nationalDashboard", name="get_dashboard", methods={"GET"})
     *
     * **/
    public function getRanda(Request $request): Response
    {

        try {

            $data = [];
            $currentYear = (int) date("Y");
            $current_month = (int) date("m");
            $current_timeslot = "T" . ceil($current_month / 3);
            $previous_timeslot = "T" . (ceil($current_month / 3) - 1);

            $randas = $this->randaRepository->findBy([
                "year" => $currentYear
            ]);
            $current_t_approved = [];
            $current_t_refused = [];
            $current_t_doing = [];
            $previous_t_approved = [];
            $others = [];
            foreach ($randas as $randa) {

                $randa_timeslot = $randa->getCurrentTimeslot();
                $randa_state = $randa->getCurrentState();
                $region = $randa->getRegion();

                $n_all_chapters = 0;
                $n_core_groups = 0;
                $n_chapters = 0;
                $n_projects = 0;

                $all_chapters = $this->chapterRepository->findBy([
                    "region" => $region
                ]);
                foreach ($all_chapters as $chapter) {
                    switch ($chapter->getCurrentState()) {
                        case "CHAPTER":
                            $n_chapters++;
                            break;
                        case "CORE_GROUP":
                            $n_core_groups++;
                            break;
                        case "PROJECT":
                            $n_projects++;
                            break;
                    }
                    $n_all_chapters++;
                }
                $region_data = [
                    "name" => $region->getName(),
                    "n_all_chapters" => $n_all_chapters,
                    "n_chapters" => $n_chapters,
                    "n_core_groups" => $n_core_groups,
                    "n_projects" => $n_projects,
                    "randa_timeslot" => $randa_timeslot,
                    "randa_state" => $randa_state
                ];
                
                switch ($randa_timeslot) {
                    case $current_timeslot:
                        switch ($randa_state) {
                            case "APPR":
                                $current_t_approved[] = $region_data;
                                break;
                            case "REFUSED":
                                $current_t_refused[] = $region_data;
                                break;
                            case "TODO":
                                $current_t_doing[] = $region_data;
                                break;
                            default:
                                $others[] = $region_data;
                        }
                        break;
                    case $previous_timeslot:
                        if ($randa_state === "APPR") {
                            $previous_t_approved[] = $region_data;
                        } else {
                            $others[] = $region_data;
                        }
                        break;
                    default:
                        $others[] = $region_data;
                }
                    
                $data_regions[] = $region_data;
            }

            $d = [
                "current_t_approved" => $current_t_approved,
                "current_t_refused" => $current_t_refused,
                "current_t_doing" => array_merge($current_t_doing, $previous_t_approved),
                "others" => $others
            ];
            $data["regions"] = $d;
            return new JsonResponse($data, Response::HTTP_OK);
        } catch (Exception $e) {
            header("exception: " . $e->getMessage());
            return new JsonResponse($e->getMessage(), Response::HTTP_OK);
        }
    }


      /**
     * Get dashboard
     *
     * @Route(path="{id}/standardDashboard", name="get_standard_dashboard", methods={"GET"})
     *
     * **/
    public function getStandardDashboard(Request $request, Region $region): Response
    {
        $data = [];
        $data["chapters_compositions"] = [
            "CHAPTER" => 0,
            "CORE_GROUP" => 0,
            "PROJECT" => 0,
            "CLOSED" => 0,
            "SUSPENDED" => 0
        ];
        
        $request = Util::normalizeRequest($request);
        $chapters = $this->chapterRepository->findBy([
            "region" => $region
        ]);
        foreach($chapters as $chapter) {
            $current_state = $chapter->getCurrentState();
            $data["chapters_compositions"][$current_state] ++;
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

}
