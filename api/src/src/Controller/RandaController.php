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

/**
 * @Route("/api")
 **/
class RandaController extends AbstractController
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
     * Create a Randa
     *
     * @Route(path="/{id}/randa", name="create_randa", methods={"POST"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The region"
     * )
     * @SWG\Parameter(
     *      name="role",
     *      in="formData",
     *      type="string",
     *      description="Optional parameter to get data relative to the specified given role"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="formData",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns a Randa object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(property="year", type="integer"),
     *          @SWG\Property(
     *              property="region",
     *              type="object",
     *              @SWG\Property(property="id", type="string"),
     *              @SWG\Property(property="name", type="string")
     *          )
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if role is given but is not valid or if randa can not be created."
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin, if a valid role is given but the user has not that role for the specified region or the role is not enought for the operation."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Randa")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function createRanda(Region $region, Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $roleCheck = [
            Constants::ROLE_EXECUTIVE
        ];
        $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
        }

        if ($code == Response::HTTP_OK) {
            $currentYear = (int) date("Y");
            $randa = $this->randaRepository->findOneBy([
                'region' => $region,
                'year' => $currentYear
            ]);

            if (!is_null($randa)) {
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

        if ($code == Response::HTTP_OK) {
            $randa = new Randa();
            $randa->setCurrentTimeslot(Constants::TIMESLOT_T0);
            $randa->setRegion($region);
            $randa->setYear($currentYear);
            $this->randaRepository->save($randa);

            return new JsonResponse($this->randaFormatter->formatBase($randa));
        } else {
            return new JsonResponse(null, $code);
        }
    }

    /**
     * Get a Randa
     *
     * @Route(path="/{id}/randa-revised", name="get_randa_revised", methods={"GET"})
     *
     * @return Response
     */
    public function getRandaRevised(Region $region, Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $timeslot = $request->get("timeslot");

        $roleCheck = [
            Constants::ROLE_EXECUTIVE
        ];
        $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
        }

        if (!$timeslot) {

            $randa = $this->randaRepository->findOneBy([
                'year' => (int) date("Y"),
                'region' => $region
            ], [
                'currentTimeslot' => 'DESC'
            ]);
            $timeslot = $randa->getCurrentTimeslot();
        } else {

            $randa = $this->randaRepository->findOneBy([
                'year' => (int) date("Y"),
                'region' => $region,
                "currentTimeslot" => $timeslot
            ]);
        }


        if ($randa) {

            $ranas = $randa->getRanas();
            $chapters = $region->getChapters();


            $data = [
                "chapters" => []
            ];

            $all_approved = true;
            foreach ($chapters as $chapter) {

                $rana = $this->ranaRepository->findOneBy([
                    "chapter" => $chapter,
                    "randa" => $randa
                ]);

                $lifecycle = $this->ranaLifecycleRepository->findOneBy([
                    "rana" => $rana,
                    "currentTimeslot" => $timeslot,
                    "currentState" => "APPR"
                ]);

                if (!$lifecycle) {
                    $all_approved = false;
                }

                if ($rana) {

                    $new_members_cons = $this->newMemberRepository->findOneBy([
                        "rana" => $rana,
                        "valueType" => "CONS",
                        "timeslot" => "T0"
                    ]);


                    $new_members_appr = $this->newMemberRepository->findOneBy(
                        [
                            "rana" => $rana,
                            "valueType" => "APPR"
                        ],
                        [
                            'timeslot' => 'DESC'
                        ]
                    );

                    $new_members_ts = [];
                    $sum = 0;
                    for ($i = 1; $i <= 12; $i++) {
                        $method = "getM$i";
                        $val = 0;
                        if ($new_members_cons) {
                            $val = $new_members_cons->$method();
                        }
                        if (is_null($val) && $new_members_appr) {
                            $val = $new_members_appr->$method();
                        }
                        $sum += $val;
                        if ($i % 3 == 0) {
                            $new_members_ts[] = $sum;
                            $sum = 0;
                        }
                    }


                    $retentions_cons = $this->retentionRepository->findOneBy([
                        "rana" => $rana,
                        "valueType" => "CONS",
                        "timeslot" => "T0"
                    ]);


                    $retentions_appr = $this->retentionRepository->findOneBy(
                        [
                            "rana" => $rana,
                            "valueType" => "APPR"
                        ],
                        [
                            'timeslot' => 'DESC'
                        ]
                    );

                    $retentions_ts = [];
                    $sum = 0;
                    for ($i = 1; $i <= 12; $i++) {
                        $method = "getM$i";
                        $val = 0;
                        if ($retentions_cons) {
                            $val = $retentions_cons->$method();
                        }
                        if (is_null($val) && $retentions_appr) {
                            $val = $retentions_appr->$method();
                        }
                        $sum += $val;
                        if ($i % 3 == 0) {
                            $retentions_ts[] = $sum;
                            $sum = 0;
                        }
                    }


                    $members = [];
                    if ($retentions_ts && $new_members_ts) {
                        for ($i = 0; $i < 4; $i++) {
                            if ($i == 0) {
                                $prev_data = $chapter->getMembers();
                            } else {
                                $prev_data = $members[$i - 1];
                            }
                            $members[] = $prev_data + ($new_members_ts[$i] - $retentions_ts[$i]);
                        }
                    }

                    $chapter_history = RandaController::getChapterHistory($rana);

                    $chapter_data = [
                        "chapter" => $chapter->getName(),
                        "state" => $chapter->getCurrentState(),
                        "initialMembers" => $chapter->getMembers(),
                        "newMembers" => $new_members_ts,
                        "retentions" => $retentions_ts,
                        "members" => $members,
                        "chapter_history" => $chapter_history,
                        "approved" => $lifecycle ? true : false
                    ];
                } else {
                    $all_approved = false;

                    $chapter_data = [
                        "chapter" => $chapter->getName(),
                        "state" => $chapter->getCurrentState(),
                        "initialMembers" => $chapter->getMembers(),
                        "newMembers" => [null, null, null, null],
                        "retentions" => [null, null, null, null],
                        "members" => [null, null, null, null],
                        "chapter_history" => [null, null, null, null],
                        "approved" => false
                    ];
                }


                $data["chapters"][] = $chapter_data;
                $data["year"] = (int) date("Y");
                $data["timeslot"] = $timeslot;
                $data["all_approved"] = $all_approved;
                $data["randa_state"] = $randa->getCurrentState();
                $data["randa_verified"] = $randa->getVerified();
                $data["region"] = $randa->getRegion()->getName();
                $data["note"] = $randa->getNote();
                $data["directors_previsions"] = $randa->getDirectorsPrevisions();
            }

            // $params = [
            //     'rana' => $rana,
            //     'timeslot' => $randa->getCurrentTimeslot(),
            //     'valueType' => Constants::VALUE_TYPE_PROP
            // ];
            // $rana->filteredNewMembers = $this->newMemberRepository->findOneBy($params);
            // $rana->filteredRenewedMembers = $this->renewedMemberRepository->findOneBy($params);
            // $rana->filteredRetentionMembers = $this->retentionRepository->findOneBy($params);

            //$randa->filteredRanas = $ranas;

            return new JsonResponse($data);
        } else {
            return new JsonResponse(false);
        }
    }

    public static function removeChaptersWithNoValues(&$chapters)
    {
        $i = 0;
        foreach ($chapters as $chapter) {
            $all_null = true;
            foreach ($chapter["data"] as $val) {
                if ($val !== null) {
                    $all_null = false;
                }
            }
            if ($all_null) {
                unset($chapters[$i]);
            }
            $i++;
        }
    }

    public static function getChapterHistory(Rana $rana)
    {
        $chapter = $rana->getChapter();
        $timeslot = $rana->getRanda()->getCurrentTimeslot();
        $number_timeslot = (int) substr($timeslot, -1);
        $currentYear = (int) date("Y");

        $chapter_history = ["CHAPTER", "CHAPTER", "CHAPTER", "CHAPTER"];

        $prev_cg = $chapter->getPrevLaunchCoregroupDate();
        $prev_c = $chapter->getPrevLaunchChapterDate();
        $actual_cg = $chapter->getActualLaunchCoregroupDate();
        $actual_c = $chapter->getActualLaunchChapterDate();

        $cg_date = $c_date = null;
        $cg_date = $actual_cg ? $actual_cg : $prev_cg;
        $c_date = $actual_c ? $actual_c : $prev_c;

        if ($cg_date) {
            if ($cg_date->format("Y") == $currentYear) {
                $m = $cg_date->format("m");
                $t = ceil($m / 3);
                for ($i = 0; $i < 4; $i++) {
                    if ($i < $t - 1) {
                        $chapter_history[$i] = "PROJECT";
                    } else {
                        $chapter_history[$i] = "CORE_GROUP";
                    }
                }
            } else if ($cg_date->format("Y") < $currentYear) {
                $chapter_history[0] = "CORE_GROUP";
                $chapter_history[1] = "CORE_GROUP";
                $chapter_history[2] = "CORE_GROUP";
                $chapter_history[3] = "CORE_GROUP";
            }
        }
        if ($c_date) {
            header("date: " . json_encode($c_date));
            if ($c_date->format("Y") == $currentYear) {
                $m = $c_date->format("m");
                $t2 = ceil($m / 3);
                for ($i = 0; $i < 4; $i++) {
                    if ($i < $t2 - 1) {
                        $chapter_history[$i] = "CORE_GROUP";
                    } else {
                        $chapter_history[$i] = "CHAPTER";
                    }
                }
            } else if ($c_date->format("Y") < $currentYear) {
                $chapter_history[0] = "CHAPTER";
                $chapter_history[1] = "CHAPTER";
                $chapter_history[2] = "CHAPTER";
                $chapter_history[3] = "CHAPTER";
            }
        }

        $susp_date = $chapter->getSuspDate();
        $res_date = $chapter->getPrevResumeDate();

        $closure_date = $chapter->getClosureDate();

        if ($susp_date) {
            if ($susp_date->format("Y") == $currentYear) {
                $m = $susp_date->format("m");
                $t = ceil($m / 3);
                for ($i = 0; $i < 4; $i++) {
                    if ($i >= $t - 1) {
                        if (!$res_date) {
                            $chapter_history[$i] = "SUSPENDED";
                        } else {
                            if ($res_date->format("Y") == $currentYear) {
                                $m2 = $res_date->format("m");
                                $t2 = ceil($m2 / 3);
                                if ($i < $t2) {
                                    $chapter_history[$i] = "SUSPENDED";
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($closure_date) {
            if ($closure_date->format("Y") == $currentYear) {
                $m = $closure_date->format("m");
                $t = ceil($m / 3);
                for ($i = 0; $i < 4; $i++) {
                    if ($i >= $t - 1) {
                        $chapter_history[$i] = "CLOSED";
                    }
                }
            }
        }


        return $chapter_history;
    }



    /**
     * Get a Randa
     *
     * @Route(path="/{id}/randa-dream", name="get_randa_dream", methods={"GET"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The region"
     * )
     * @SWG\Parameter(
     *      name="role",
     *      in="formData",
     *      type="string",
     *      description="Optional parameter to get data relative to the specified given role"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="formData",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns a Randa object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="currentTimeslot", type="string"),
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(
     *              property="region",
     *              type="object",
     *              @SWG\Property(property="id", type="string"),
     *              @SWG\Property(property="name", type="string")
     *          ),
     *          @SWG\Property(property="year", type="integer"),
     *          @SWG\Property(
     *              property="ranas",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="chapter",
     *                      type="object",
     *                      @SWG\Property(property="id", type="string"),
     *                      @SWG\Property(property="name", type="string")
     *                  ),
     *                  @SWG\Property(property="id", type="string"),
     *                  @SWG\Property(
     *                      property="newMembers",
     *                      type="object",
     *                      @SWG\Property(
     *                          property="PROP",
     *                          type="object",
     *                          @SWG\Property(property="m1", type="integer"),
     *                          @SWG\Property(property="m2", type="integer"),
     *                          @SWG\Property(property="m3", type="integer"),
     *                          @SWG\Property(property="m4", type="integer"),
     *                          @SWG\Property(property="m5", type="integer"),
     *                          @SWG\Property(property="m6", type="integer"),
     *                          @SWG\Property(property="m7", type="integer"),
     *                          @SWG\Property(property="m8", type="integer"),
     *                          @SWG\Property(property="m9", type="integer"),
     *                          @SWG\Property(property="m10", type="integer"),
     *                          @SWG\Property(property="m11", type="integer"),
     *                          @SWG\Property(property="m12", type="integer")
     *                      )
     *                  ),
     *                  @SWG\Property(
     *                      property="renewedMembers",
     *                      type="object",
     *                      @SWG\Property(
     *                          property="PROP",
     *                          type="object",
     *                          @SWG\Property(property="m1", type="integer"),
     *                          @SWG\Property(property="m2", type="integer"),
     *                          @SWG\Property(property="m3", type="integer"),
     *                          @SWG\Property(property="m4", type="integer"),
     *                          @SWG\Property(property="m5", type="integer"),
     *                          @SWG\Property(property="m6", type="integer"),
     *                          @SWG\Property(property="m7", type="integer"),
     *                          @SWG\Property(property="m8", type="integer"),
     *                          @SWG\Property(property="m9", type="integer"),
     *                          @SWG\Property(property="m10", type="integer"),
     *                          @SWG\Property(property="m11", type="integer"),
     *                          @SWG\Property(property="m12", type="integer")
     *                      )
     *                  ),
     *                  @SWG\Property(
     *                      property="retention",
     *                      type="object",
     *                      @SWG\Property(
     *                          property="PROP",
     *                          type="object",
     *                          @SWG\Property(property="m1", type="integer"),
     *                          @SWG\Property(property="m2", type="integer"),
     *                          @SWG\Property(property="m3", type="integer"),
     *                          @SWG\Property(property="m4", type="integer"),
     *                          @SWG\Property(property="m5", type="integer"),
     *                          @SWG\Property(property="m6", type="integer"),
     *                          @SWG\Property(property="m7", type="integer"),
     *                          @SWG\Property(property="m8", type="integer"),
     *                          @SWG\Property(property="m9", type="integer"),
     *                          @SWG\Property(property="m10", type="integer"),
     *                          @SWG\Property(property="m11", type="integer"),
     *                          @SWG\Property(property="m12", type="integer")
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if role is given but is not valid or if randa can not be created."
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin, if a valid role is given but the user has not that role for the specified region or the role is not enought for the operation."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Randa")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function getRandaDream(Region $region, Request $request): Response
    {
        $roleCheck = [
            Constants::ROLE_EXECUTIVE
        ];
        $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
        }

        $randa = $this->randaRepository->findOneBy([
            'year' => (int) date("Y"),
            'region' => $region
        ]);

        if ($randa) {

            $timeslot = $randa->getCurrentTimeslot();
            if ($randa->getCurrentState() == "TODO") {
                $num = substr($timeslot, -1);
                if ($num > 0) {
                    $timeslot = "T" + ($num * 1 - 1);
                } else {
                    return new JsonResponse(false);
                }
            }

            $ranas = $randa->getRanas();

            $data = [
                "chapters" => []
            ];

            foreach ($ranas as $rana) {

                $chapter = $rana->getChapter();
                $chapter_history = RandaController::getChapterHistory($rana);

                $new_members_cons = $this->newMemberRepository->findOneBy([
                    "rana" => $rana,
                    "valueType" => "CONS",
                    "timeslot" => "T0"
                ]);


                $new_members_appr = $this->newMemberRepository->findOneBy(
                    [
                        "rana" => $rana,
                        "valueType" => "PROP"
                    ],
                    [
                        'timeslot' => 'DESC'
                    ]
                );

                $new_members_ts = [];
                $sum = 0;
                for ($i = 1; $i <= 12; $i++) {
                    $method = "getM$i";
                    $val = 0;
                    if ($new_members_cons) {
                        $val = $new_members_cons->$method();
                    }
                    if (is_null($val) && $new_members_appr) {
                        $val = $new_members_appr->$method();
                    }
                    $sum += $val;
                    if ($i % 3 == 0) {
                        $new_members_ts[] = $sum;
                        $sum = 0;
                    }
                }


                $retentions_cons = $this->retentionRepository->findOneBy([
                    "rana" => $rana,
                    "valueType" => "CONS",
                    "timeslot" => "T0"
                ]);


                $retentions_appr = $this->retentionRepository->findOneBy(
                    [
                        "rana" => $rana,
                        "valueType" => "PROP"
                    ],
                    [
                        'timeslot' => 'DESC'
                    ]
                );

                $retentions_ts = [];
                $sum = 0;
                for ($i = 1; $i <= 12; $i++) {
                    $method = "getM$i";
                    $val = 0;
                    if ($retentions_cons) {
                        $val = $retentions_cons->$method();
                    }
                    if (is_null($val) && $retentions_appr) {
                        $val = $retentions_appr->$method();
                    }
                    $sum += $val;
                    if ($i % 3 == 0) {
                        $retentions_ts[] = $sum;
                        $sum = 0;
                    }
                }


                $members = [];
                if ($retentions_ts && $new_members_ts) {
                    for ($i = 0; $i < 4; $i++) {
                        if ($i == 0) {
                            $prev_data = $chapter->getMembers();
                        } else {
                            $prev_data = $members[$i - 1];
                        }
                        $members[] = $prev_data + ($new_members_ts[$i] - $retentions_ts[$i]);
                    }
                }


                $chapter_data = [
                    "chapter" => $chapter->getName(),
                    "initialMembers" => $chapter->getMembers(),
                    "newMembers" => $new_members_ts,
                    "retentions" => $retentions_ts,
                    "members" => $members,
                ];

                $data["chapters"][] = $chapter_data;
                $data["year"] = (int) date("Y");
                $data["timeslot"] = $timeslot;
                $data["chapter_history"] = $chapter_history;
            }
            return new JsonResponse($data);
        } else {
            return new JsonResponse(false);
        }


        // $params = [
        //     'rana' => $rana,
        //     'timeslot' => $randa->getCurrentTimeslot(),
        //     'valueType' => Constants::VALUE_TYPE_PROP
        // ];
        // $rana->filteredNewMembers = $this->newMemberRepository->findOneBy($params);
        // $rana->filteredRenewedMembers = $this->renewedMemberRepository->findOneBy($params);
        // $rana->filteredRetentionMembers = $this->retentionRepository->findOneBy($params);

        //$randa->filteredRanas = $ranas;

    }


    /**
     * Get a Randa
     *
     * @Route(path="/{id}/approve-randa", name="approve_randa", methods={"PUT"})
     *
     * 
     *
     * @return Response
     */
    public function approveRanda(Region $region, Request $request): Response
    {
        $note = $request->get("note");
        $directors = $request->get("directors");
        $timeslot = $request->get("timeslot");
        $currentYear = (int) date("Y");

        $randa = $this->randaRepository->findOneBy([
            "region" => $region,
            "year" => $currentYear,
            "currentTimeslot" => $timeslot
        ]);

        if ($randa) {

            $randa->setCurrentState("APPR");
            $randa->setCurrentTimeslot($timeslot);
            $randa->setDirectorsPrevisions($directors);
            $randa->setNote($note);
            $this->randaRepository->save($randa);
        }


        // $chapters = $this->chapterRepository->findBy([
        //     "region" => $region
        // ]);

        // foreach ($chapters as $chapter) {


        //     $rana = $this->ranaRepository->findOneBy([
        //         "randa" => $randa,
        //         "chapter" => $chapter
        //     ]);

        //     if (!$rana) {
        //         $rana = new Rana();
        //         $rana->setChapter($chapter);
        //         $rana->setRanda($randa);
        //         $this->ranaRepository->save($rana);
        //     }

        //     $lc = $this->ranaLifecycleRepository->findOneBy([
        //         "currentTimeslot" => $timeslot,
        //         "rana" => $rana
        //     ]);

        //     if (!$lc) {
        //         $ranaLifeCycle = new RanaLifecycle();
        //         $ranaLifeCycle->setCurrentState(Constants::RANA_LIFECYCLE_STATUS_TODO);
        //         $ranaLifeCycle->setCurrentTimeslot($timeslot);
        //         $ranaLifeCycle->setRana($rana);
        //         $this->ranaLifecycleRepository->save($ranaLifeCycle);
        //         $this->entityManager->refresh($rana);
        //     }
        // }

        // $this->randaRepository->save($randa);
        return new JsonResponse(true);
    }



    /**
     * Get a Randa
     *
     * @Route(path="/{id}/refuse-randa", name="refuse_randa", methods={"PUT"})
     *
     * 
     *
     * @return Response
     */
    public function refuseRanda(Region $region, Request $request): Response
    {
        $refuseNote = $request->get("refuseNote");
        $currentYear = (int) date("Y");
        $randa = $this->randaRepository->findOneBy([
            "region" => $region,
            "year" => $currentYear
        ]);
        $timeslot = $randa->getCurrentTimeslot();
        if ($randa) {
            $randa->setCurrentTimeslot($timeslot);
            $randa->setCurrentState("REFUSED");
            $randa->setRefuseNote($refuseNote);
            $this->randaRepository->save($randa);

            $title = "RANDA rifiutato: " . $region->getName() . " " . $randa->getYear() . " " . $randa->getCurrentTimeslot();

            $data = [
                "randa_year" => $randa->getYear(),
                "randa_timeslot" => $randa->getCurrentTimeslot(),
                "randa_region" => $region->getName(),
                "title" => $title
            ];

            $distinct_directors = [];
            $directors = $this->directorRepository->findBy([
                "role" => "EXECUTIVE",
                "region" => $region
            ]);
            foreach ($directors as $director) {
                if (!in_array($director->getId(), $distinct_directors)) {
                    $distinct_directors[] = $director->getId();
                    $email = (new TemplatedEmail())
                        ->from('rosbi@studio-mercurio.it')
                        ->to($director->getUser()->getEmail())
                        ->subject($title)
                        ->htmlTemplate('emails/randa-refused/html.twig')
                        ->context($data);

                    $this->mailer->send($email);
                }
            }
        }
        return new JsonResponse(true);
    }


    /**
     * Get a Randa
     *
     * @Route(path="/{id}/verify-randa", name="verify_randa", methods={"PUT"})
     *
     * 
     *
     * @return Response
     */
    public function verifyRanda(Region $region, Request $request): Response
    {
        $currentYear = (int) date("Y");
        $randa = $this->randaRepository->findOneBy([
            "region" => $region,
            "year" => $currentYear
        ]);

        if ($randa) {
            $randa->setVerified(true);
            $randa->setCurrentState("APPR");
            $this->randaRepository->save($randa);
        }
        return new JsonResponse(true);
    }



    /**
     * Get a Randa
     *
     * @Route(path="/{id}/create-next-timeslot", name="create_next_timeslot", methods={"PUT"})
     *
     * 
     *
     * @return Response
     */
    public function createNextTimeslot(Region $region, Request $request): Response
    {

        try {
            $currentYear = (int) date("Y");
            $randa = $this->randaRepository->findOneBy([
                "region" => $region,
                "year" => $currentYear
            ]);

            $timeslot = $randa->getCurrentTimeslot();
            $slotNumber = (int) substr($timeslot, -1);
            $nextSlotNumber = $slotNumber + 1;
            $nextTimeslot = "T$nextSlotNumber";

            if ($nextSlotNumber == 5) {
                $randa = new Randa();
                $randa->setRegion($region);
                $randa->setYear($currentYear + 1);
                $nextTimeslot = "T0";
            }

            $randa->setCurrentTimeslot($nextTimeslot);
            $randa->setCurrentState("TODO");

            $this->randaRepository->save($randa);

            $chapters = $this->chapterRepository->findBy([
                "region" => $region
            ]);

            $directors = [];

            foreach ($chapters as $chapter) {
                $rana = $this->ranaRepository->findOneBy([
                    "randa" => $randa,
                    "chapter" => $chapter
                ]);
                if (!$rana) {
                    $rana = new Rana();
                    $rana->setChapter($chapter);
                    $rana->setRanda($randa);
                    $this->ranaRepository->save($rana);
                }

                $lc = $this->ranaLifecycleRepository->findOneBy([
                    "rana" => $rana,
                    "currentTimeslot" => $nextTimeslot
                ]);

                if (!$lc) {
                    $ranaLifeCycle = new RanaLifecycle();
                    $ranaLifeCycle->setCurrentState("TODO");
                    $ranaLifeCycle->setCurrentTimeslot($nextTimeslot);
                    $ranaLifeCycle->setRana($rana);
                    $this->ranaLifecycleRepository->save($ranaLifeCycle);
                    $this->entityManager->refresh($rana);
                }

                $director = $chapter->getDirector();

                if (!in_array($director->getId(), $directors)) {
                    $directors[] = $director->getId();

                    $to_email = $director->getUser()->getEmail();
                    $to_name = $director->getUser()->getFullName();
                    //$temp_subject = $to_email . " ---- " . $to_name; //toremove

                    $title = "Inizio compilazione RANDA " . $randa->getYear() . " " . $randa->getCurrentTimeslot();

                    $data = [
                        "randa_year" => $randa->getYear(),
                        "randa_timeslot" => $randa->getCurrentTimeslot(),
                        "randa_region" => $region->getName(),
                        "title" => $title
                    ];

                    $email = (new TemplatedEmail())
                        ->from('rosbi@studio-mercurio.it')
                        ->to($to_email)
                        ->subject($title)
                        ->htmlTemplate('emails/next-randa-start/html.twig')
                        ->context($data);

                    $this->mailer->send($email);
                }
            }
            return new JsonResponse(true, Response::HTTP_OK);
        } catch (Exception $e) {
            header("exception: " . $e->getMessage());
            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * Get a Randa
     *
     * @Route(path="/{id}/randa", name="get_randa", methods={"GET"})
     *
     * **/
    public function getRanda(Region $region, Request $request): Response
    {

        try {
            $currentYear = (int) date("Y");

            $randa = $this->randaRepository->findOneBy([
                "region" => $region,
                "year" => $currentYear
            ]);

            if ($randa) {
                $timeslot = $randa->getCurrentTimeslot();

                if ($randa->getCurrentState() == "TODO") {
                    $num = (int) substr($timeslot, -1);
                    if ($num > 0) {
                        $timeslot = "T" . ($num * 1 - 1);
                    } else {
                        return new JsonResponse(false);
                    }
                }

                $data["core_groups_new"] = [];
                $data["core_groups_ret"] = [];
                $data["core_groups_act"] = [];
                $data["chapters_new"] = [];
                $data["chapters_ret"] = [];
                $data["chapters_act"] = [];
                // $data["chapters_average"] = [];
                // $data["core_groups_average"] = [];
				

                $data["directors"] = null;
                $data["num_chapters"] = [0, 0, 0, 0];
                $data["note"] = $randa->getNote() ? $randa->getNote() : "Nessuna nota";
                $data["timeslot"] = $timeslot;
                $data["year"] = $currentYear;

                $roleCheck = [
                    Constants::ROLE_EXECUTIVE
                ];

                $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));

                foreach ($performerData as $var => $value) {
                    $$var = $value;
                }

                $projects = $this->chapterRepository->findBy([
                    "region" => $region,
                    "currentState" => "PROJECT"
                ]);

                $core_groups = $this->chapterRepository->findBy([
                    "region" => $region,
                    "currentState" => "CORE_GROUP"
                ]);

                foreach ($core_groups as $core_group) {


                    if ($core_group->getPrevLaunchChapterDate()) {
                        $year = $core_group->getPrevLaunchChapterDate()->format("Y");
                        $month = $core_group->getPrevLaunchChapterDate()->format("m");
                    } else {
                        $year = 2021;
                        $month = 13;
                    }

                    $initial_members = $core_group->getMembers();
                    if ($year >= (int) date("Y")) {

                        $chapter_element_cg_ret = [
                            "name" => $core_group->getName(),
                            "data" => [],
                            "initial" => $core_group->getMembers()
                        ];
                        $chapter_element_cg_new = [
                            "name" => $core_group->getName(),
                            "data" => [],
                            "initial" => $core_group->getMembers()
                        ];
                        $chapter_element_cg_act = [
                            "name" => $core_group->getName(),
                            "data" => [],
                            "initial" => $core_group->getMembers()
                        ];
                        $chapter_element_ret = [
                            "name" => $core_group->getName(),
                            "data" => [],
                            "initial" => $core_group->getMembers()
                        ];
                        $chapter_element_act = [
                            "name" => $core_group->getName(),
                            "data" => [],
                            "initial" => $core_group->getMembers()
                        ];

                        $chapter_element_new = [
                            "name" => $core_group->getName(),
                            "data" => [],
                            "initial" => $core_group->getMembers()
                        ];
						


                        $rana = $this->ranaRepository->findOneBy([
                            "chapter" => $core_group,
                            "randa" => $randa
                        ]);

                        $new_cons = $this->newMemberRepository->findOneBy([
                            "rana" => $rana,
                            "timeslot" => "T0",
                            "valueType" => "CONS"
                        ]);

                        $new_appr = $this->newMemberRepository->findOneBy([
                            "rana" => $rana,
                            "timeslot" => $timeslot,
                            "valueType" => "APPR"
                        ]);

                        $ret_cons = $this->retentionRepository->findOneBy([
                            "rana" => $rana,
                            "timeslot" => "T0",
                            "valueType" => "CONS"
                        ]);

                        $ret_appr = $this->retentionRepository->findOneBy([
                            "rana" => $rana,
                            "timeslot" => $timeslot,
                            "valueType" => "APPR"
                        ]);

                        $new_members = 0;
                        $ret_members = 0;
                        for ($i = 1; $i <= 12; $i++) {
                            $method = "getM$i";
                            $new_members += ($new_cons && $new_cons->$method() !== null) ? $new_cons->$method() : ($new_appr && $new_appr !== null ? $new_appr->$method() : null);
                            $ret_members += ($ret_cons && $ret_cons->$method() !== null)  ? $ret_cons->$method() : ($ret_appr && $ret_appr !== null ? $ret_appr->$method() : null);

                            if ($i % 3 == 0) {
                                // è ancora un core group
                                if ($i < $month || $year > $currentYear) {

                                    
                                    $chapter_element_cg_new["data"][] = $new_members;
                                    $chapter_element_cg_ret["data"][] = $ret_members;
                                    $cg_len = sizeof($chapter_element_cg_act["data"]);
                                    $initial_cg_members = $cg_len ? $chapter_element_cg_act["data"][$cg_len - 1] : $core_group->getMembers();
                                    $chapter_element_cg_act["data"][] = $initial_cg_members + ($new_members - $ret_members);
                                    if($month == 13 && $core_group->getName() == "BNI Topaz") {
                                        header("chapter:" . $initial_cg_members);
                                    }

                                    $chapter_element_new["data"][] = null;
                                    $chapter_element_ret["data"][] = null;
                                    $chapter_element_act["data"][] = null;

                                } else {

                                    $slot = ceil($i / 3) - 1;

                                    $chapter_element_new["data"][] = $new_members;
                                    $chapter_element_ret["data"][] = $ret_members;
                                    $c_len = sizeof($chapter_element_cg_act["data"]);
                                    $was_chapter_in_prev_slot = $slot > 0 && sizeof($chapter_element_act["data"]) && $chapter_element_act["data"][$slot - 1] !== null;
                                    if ($was_chapter_in_prev_slot) {
                                        $prev_val = $chapter_element_act["data"][$slot - 1];
                                    } else if (sizeof($chapter_element_cg_act["data"])) {
                                        $prev_val = $chapter_element_cg_act["data"][$slot - 1];
                                    } else {
                                        $prev_val = $core_group->getMembers();
                                    }
                                    $initial_members = $prev_val;
                                    $chapter_element_act["data"][] = $initial_members + ($new_members - $ret_members);


                                    $data["num_chapters"][$slot]++;

                                    $chapter_element_cg_act["data"][] = null;
                                    $chapter_element_cg_new["data"][] = null;
                                    $chapter_element_cg_ret["data"][] = null;
                                }

                                $new_members = 0;
                                $ret_members = 0;
                            }
                        }
                        $data["core_groups_new"][] = $chapter_element_cg_new;
                        $data["core_groups_ret"][] = $chapter_element_cg_ret;
                        $data["core_groups_act"][] = $chapter_element_cg_act;
                        $data["chapters_new"][] = $chapter_element_new;
                        $data["chapters_ret"][] = $chapter_element_ret;
                        $data["chapters_act"][] = $chapter_element_act;
                    }
                }

                $chapters = $this->chapterRepository->findBy([
                    "region" => $region,
                    "currentState" => "CHAPTER"
                ]);

                foreach ($chapters as $chapter) {


                    $initial_members = $chapter->getMembers();
                    $chapter_element_ret = [
                        "name" => $chapter->getName(),
                        "data" => [],
                        "initial" => $initial_members
                    ];
                    $chapter_element_act = [
                        "name" => $chapter->getName(),
                        "data" => [],
                        "initial" => $initial_members
                    ];

                    $chapter_element_new = [
                        "name" => $chapter->getName(),
                        "data" => [],
                        "initial" => $initial_members
                    ];

                    $rana = $this->ranaRepository->findOneBy([
                        "chapter" => $chapter,
                        "randa" => $randa
                    ]);

                    $new_cons = $this->newMemberRepository->findOneBy([
                        "rana" => $rana,
                        "timeslot" => "T0",
                        "valueType" => "CONS"
                    ]);

                    $new_appr = $this->newMemberRepository->findOneBy([
                        "rana" => $rana,
                        "timeslot" => $timeslot,
                        "valueType" => "APPR"
                    ]);

                    $ret_cons = $this->retentionRepository->findOneBy([
                        "rana" => $rana,
                        "timeslot" => "T0",
                        "valueType" => "CONS"
                    ]);
                    $ret_appr = $this->retentionRepository->findOneBy([
                        "rana" => $rana,
                        "timeslot" => $timeslot,
                        "valueType" => "APPR"
                    ]);

                    $new_members = 0;
                    $ret_members = 0;
                    for ($i = 1; $i <= 12; $i++) {
                        $method = "getM$i";
                        $new_members += ($new_cons && $new_cons->$method() !== null)  ? $new_cons->$method() : ($new_appr && $new_appr !== null ? $new_appr->$method() : null);
                        $ret_members += ($ret_cons && $ret_cons->$method() !== null)  ? $ret_cons->$method() : ($ret_appr && $ret_appr !== null ? $ret_appr->$method() : null);
                        if ($i % 3 == 0) {

                            $chapter_element_new["data"][] = $new_members;
                            $chapter_element_ret["data"][] = $ret_members;


                            $act_len = sizeof($chapter_element_act["data"]);
                            $chapter_element_act["data"][] = ($act_len ? $chapter_element_act["data"][$act_len - 1] : $initial_members) + ($new_members - $ret_members);

                            $slot = ceil($i / 3) - 1;
                            $data["num_chapters"][$slot]++;

                            $new_members = 0;
                            $ret_members = 0;
                        }
                    }
                    $data["chapters_new"][] = $chapter_element_new;
                    $data["chapters_ret"][] = $chapter_element_ret;
                    $data["chapters_act"][] = $chapter_element_act;
                }
                foreach ($projects as $project) {
                    if ($project->getPrevLaunchCoreGroupDate()) {
                        $y_cg = $project->getPrevLaunchCoreGroupDate()->format("Y");
                        if ($y_cg > (int) date("Y")) {
                            $month_cg = 13;
                            $month_c = 13;
                        } else {
                            $month_cg = $project->getPrevLaunchCoreGroupDate()->format("m");
                            if ($project->getPrevLaunchChapterDate()) {
                                $y_c = $project->getPrevLaunchChapterDate()->format("Y");
                                if ($y_c > (int) date("Y")) {
                                    $month_c = 13;
                                } else {
                                    $month_c = $project->getPrevLaunchChapterDate()->format("m");
                                }
                            } else {
                                $month_c = 13;
                            }
                        }
                    } else {
                        $month_cg = 13;
                        $month_c = 13;
                    }

                    if ($month_cg < 13) {

                        $initial_members = $project->getMembers();
						
						$chapter_element_p_ret = [
                            "name" => $project->getName(),
                            "data" => [],
                            "initial" => $project->getMembers()
                        ];
                        $chapter_element_p_new = [
                            "name" => $project->getName(),
                            "data" => [],
                            "initial" => $project->getMembers()
                        ];
                        $chapter_element_p_act = [
                            "name" => $project->getName(),
                            "data" => [],
                            "initial" => $project->getMembers()
                        ];
						

                        $chapter_element_cg_ret = [
                            "name" => $project->getName(),
                            "data" => [],
                            "initial" => $project->getMembers()
                        ];
                        $chapter_element_cg_new = [
                            "name" => $project->getName(),
                            "data" => [],
                            "initial" => $project->getMembers()
                        ];
                        $chapter_element_cg_act = [
                            "name" => $project->getName(),
                            "data" => [],
                            "initial" => $project->getMembers()
                        ];
                        $chapter_element_ret = [
                            "name" => $project->getName(),
                            "data" => [],
                            "initial" => $project->getMembers()
                        ];
                        $chapter_element_act = [
                            "name" => $project->getName(),
                            "data" => [],
                            "initial" => $project->getMembers()
                        ];

                        $chapter_element_new = [
                            "name" => $project->getName(),
                            "data" => [],
                            "initial" => $project->getMembers()
                        ];

                        $rana = $this->ranaRepository->findOneBy([
                            "chapter" => $project,
                            "randa" => $randa
                        ]);

                        $new_cons = $this->newMemberRepository->findOneBy([
                            "rana" => $rana,
                            "valueType" => "CONS"
                        ]);
                        $new_appr = $this->newMemberRepository->findOneBy([
                            "rana" => $rana,
                            "timeslot" => $timeslot,
                            "valueType" => "APPR"
                        ]);
                        $ret_cons = $this->retentionRepository->findOneBy([
                            "rana" => $rana,
                            "valueType" => "CONS"
                        ]);
                        $ret_appr = $this->retentionRepository->findOneBy([
                            "rana" => $rana,
                            "timeslot" => $timeslot,
                            "valueType" => "APPR"
                        ]);
                        $new_members = 0;
                        $ret_members = 0;
                        for ($i = 1; $i <= 12; $i++) {

                            $method = "getM$i";
                            $new_members += ($new_cons && $new_cons->$method() !== null) ? $new_cons->$method() : ($new_appr && $new_appr !== null ? $new_appr->$method() : 0);
                            $ret_members += ($ret_cons && $ret_cons->$method() !== null) ? $ret_cons->$method() : ($ret_appr && $ret_appr !== null ? $ret_appr->$method() : 0);
                            if ($i % 3 == 0) {

                                if ($i >= $month_c) {
                                    $chapter_element_new["data"][] = $new_members;
                                    $chapter_element_ret["data"][] = $ret_members;
                                    $act_len = sizeof($chapter_element_act["data"]);
                                    $chapter_element_act["data"][] = ($act_len ? $chapter_element_act["data"][$act_len - 1] : $initial_members) + ($new_members - $ret_members);

                                    $slot = ceil($i / 3) - 1;
                                    $data["num_chapters"][$slot]++;

                                    $chapter_element_cg_act["data"][] = null;
                                    $chapter_element_cg_new["data"][] = null;
                                    $chapter_element_cg_ret["data"][] = null;
                                } else if ($i >= $month_cg) {

									$slot = ceil($i / 3) - 1;
							
                                    $chapter_element_cg_new["data"][] = $new_members;
                                    $chapter_element_cg_ret["data"][] = $ret_members;
									

									$cg_len = sizeof($chapter_element_cg_act["data"]);
                                    $was_core_in_prev_slot = $slot > 0 && sizeof($chapter_element_cg_act["data"]) && (isset($chapter_element_cg_act["data"][$slot - 1]) && $chapter_element_cg_act["data"][$slot - 1] !== null);
									
									$was_project_in_prev_slot = $slot > 0 && sizeof($chapter_element_p_act["data"]) && (isset($chapter_element_p_act["data"][$slot - 1]) && $chapter_element_p_act["data"][$slot - 1] !== null);

                                    if ($was_core_in_prev_slot) {
                                        $prev_val = $chapter_element_cg_act["data"][$slot - 1];
                                    } else if($was_project_in_prev_slot) {
										$prev_val = $chapter_element_p_act["data"][$slot - 1];
									} else {
                                        $prev_val = $project->getMembers();
                                    }
                                    $initial_members = $prev_val;
                                    $chapter_element_cg_act["data"][] = $initial_members + ($new_members - $ret_members);


									
                                    
                                    $chapter_element_new["data"][] = null;
                                    $chapter_element_ret["data"][] = null;
                                    $chapter_element_act["data"][] = null;
                                } else {
									
									$chapter_element_p_new["data"][] = $new_members;
                            $chapter_element_p_ret["data"][] = $ret_members;
									
									
                                    $p_len = sizeof($chapter_element_p_act["data"]);
                                    $initial_members = $p_len ? $chapter_element_p_act["data"][$p_len - 1] : $initial_members;
									$val = $initial_members + ($new_members - $ret_members);;
                                    $chapter_element_p_act["data"][] = $val;


                                    $chapter_element_new["data"][] = null;
                                    $chapter_element_ret["data"][] = null;
                                    $chapter_element_act["data"][] = null;
                                    $chapter_element_cg_act["data"][] = null;
                                    $chapter_element_cg_new["data"][] = null;
                                    $chapter_element_cg_ret["data"][] = null;
					
                                }

                                $new_members = 0;
                                $ret_members = 0;
                            }
                        }

                        $data["core_groups_new"][] = $chapter_element_cg_new;
                        $data["core_groups_ret"][] = $chapter_element_cg_ret;
                        $data["core_groups_act"][] = $chapter_element_cg_act;
                        $data["chapters_new"][] = $chapter_element_new;
                        $data["chapters_ret"][] = $chapter_element_ret;
                        $data["chapters_act"][] = $chapter_element_act;
						
                    }
                }

                $closed = $this->chapterRepository->findBy([
                    "region" => $region,
                    "currentState" => "CLOSED"
                ]);

                foreach ($closed as $clo) {
                    if ($clo->getClosureDate()) {
                        $y_c = $clo->getClosureDate()->format("Y");
                        if ($y_c == (int) date("Y")) {
                            $month = $clo->getClosureDate()->format("m");

                            if ($month > 3) {

                                $initial_members = $clo->getMembers();

                                $chapter_element_ret = [
                                    "name" => $clo->getName(),
                                    "data" => [],
                                    "initial" => $clo->getMembers()
                                ];
                                $chapter_element_act = [
                                    "name" => $clo->getName(),
                                    "data" => [],
                                    "initial" => $clo->getMembers()
                                ];


                                $chapter_element_new = [
                                    "name" => $clo->getName(),
                                    "data" => [],
                                    "initial" => $clo->getMembers()
                                ];

                                $rana = $this->ranaRepository->findOneBy([
                                    "chapter" => $clo,
                                    "randa" => $randa
                                ]);

                                $new_cons = $this->newMemberRepository->findOneBy([
                                    "rana" => $rana,
                                    "valueType" => "CONS"
                                ]);
                                $new_appr = $this->newMemberRepository->findOneBy([
                                    "rana" => $rana,
                                    "timeslot" => $timeslot,
                                    "valueType" => "APPR"
                                ]);
                                $ret_cons = $this->retentionRepository->findOneBy([
                                    "rana" => $rana,
                                    "valueType" => "CONS"
                                ]);
                                $ret_appr = $this->retentionRepository->findOneBy([
                                    "rana" => $rana,
                                    "timeslot" => $timeslot,
                                    "valueType" => "APPR"
                                ]);
                                $new_members = 0;
                                $ret_members = 0;
                                for ($i = 1; $i <= 12; $i++) {

                                    $method = "getM$i";
                                    $new_members += ($new_cons && $new_cons->$method() !== null) ? $new_cons->$method() : ($new_appr && $new_appr !== null ? $new_appr->$method() : 0);
                                    $ret_members += ($ret_cons && $ret_cons->$method() !== null) ? $ret_cons->$method() : ($ret_appr && $ret_appr !== null ? $ret_appr->$method() : 0);
                                    if ($i % 3 == 0) {

                                        if ($i < $month) {
                                            $chapter_element_new["data"][] = $new_members;
                                            $chapter_element_ret["data"][] = $ret_members;
                                            $act_len = sizeof($chapter_element_act["data"]);
                                            $chapter_element_act["data"][] = ($act_len ? $chapter_element_act["data"][$act_len - 1] : $initial_members) + ($new_members - $ret_members);

                                            $slot = ceil($i / 3) - 1;
                                            $data["num_chapters"][$slot]++;
                                        } else {
                                            $chapter_element_new["data"][] = null;
                                            $chapter_element_ret["data"][] = null;
                                            $chapter_element_act["data"][] = null;
                                        }

                                        $new_members = 0;
                                        $ret_members = 0;
                                    }
                                }

                                $data["chapters_new"][] = $chapter_element_new;
                                $data["chapters_ret"][] = $chapter_element_ret;
                                $data["chapters_act"][] = $chapter_element_act;
                            }
                        }
                    }
                }





                //calcolo membri


                // file_put_contents("mail_log", sizeof($data["core_groups_new"]), FILE_APPEND);
                // for ($j = 0; $j < sizeof($data["core_groups_new"]); $j++) {
                //     $cgn = $data["core_groups_new"][$j];
                //     $cgr =  $data["core_groups_ret"][$j];

                //     $cga = [
                //         "name" => $cgn["name"]
                //     ];
                //     $d = [];
                //     for ($i = 0; $i < 4; $i++) {
                //         $initial = $cgn["initial"];
                //         if ($i > 0) {
                //             $initial = $d[$i - 1];
                //         }
                //         $d[$i] = $initial + $cgn["data"][$i] - $cgr["data"][$i];
                //     }
                //     $cga["data"] = $d;
                //     $data["core_groups_act"][] = $cga;
                //     file_put_contents("mail_log", "CORE GROUP: " . sizeof($data["core_groups_act"]), FILE_APPEND);
                // }

                // for ($j = 0; $j < sizeof($data["chapters_new"]); $j++) {
                //     $cn = $data["chapters_new"][$j];
                //     $cr = $data["chapters_ret"][$j];

                //     $ca = [
                //         "name" => $cn["name"]
                //     ];
                //     $d = [];
                //     for ($i = 0; $i < 4; $i++) {
                //         $initial = $cn["initial"];
                //         if ($i > 0) {
                //             $initial = $d[$i - 1];
                //         }
                //         $n = $cn["data"][$i];
                //         $r = $cr["data"][$i];
                //         $d[$i] = $initial + $n - $r;
                //     }
                //     $ca["data"] = $d;
                //     $data["chapters_act"][] = $ca;
                // }


                $cg_sums = [0, 0, 0, 0];
                $chapter_sums = [0, 0, 0, 0];

                foreach ($data["core_groups_act"] as $cg) {
                    for ($i = 0; $i < 4; $i++) {
                        $cg_sums[$i] += $cg["data"][$i];
                    }
                }
                foreach ($data["chapters_act"] as $cg) {
                    for ($i = 0; $i < 4; $i++) {
                        $chapter_sums[$i] += $cg["data"][$i];
                    }
                }
                // for ($i = 0; $i < 4; $i++) {
                //     $data["chapters_average"][$i] = 0;
                //     if (sizeof($data["chapters_act"])) {
                //         $data["chapters_average"][$i] = round($chapter_sums[$i] / sizeof($data["chapters_act"]));
                //     }
                //     $data["core_groups_average"][$i] = 0;
                //     if (sizeof($data["core_groups_act"])) {
                //         $data["core_groups_average"][$i] = round($chapter_sums[$i] / sizeof($data["core_groups_act"]));
                //     }
                // }

                RandaController::removeChaptersWithNoValues($data["chapters_new"]);
                RandaController::removeChaptersWithNoValues($data["chapters_act"]);
                RandaController::removeChaptersWithNoValues($data["chapters_ret"]);

                $directors = $randa->getDirectorsPrevisions() ? $randa->getDirectorsPrevisions() : "0,0,0,0";
                $data["directors"] = explode(",", $directors);
                $data["randa_state"] = $randa->getCurrentState();
                $data["region"] = $randa->getRegion()->getName();
                $data["randa_verified"] = $randa->getVerified();


                return new JsonResponse($data);
            } else {
                return new JsonResponse(false);
            }
        } catch (Exception $e) {
            return new JsonResponse("E" . $e->getMessage() . $e->getLine());
        }
    }
}
