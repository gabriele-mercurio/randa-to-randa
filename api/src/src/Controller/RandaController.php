<?php

namespace App\Controller;

use Exception;
use App\Util\Util;
use App\Entity\Rana;
use App\Entity\Randa;
use App\Entity\Region;
use App\Util\Constants;
use App\Entity\NewMember;
use App\Entity\Retention;
use App\Entity\RanaLifecycle;
use Swagger\Annotations as SWG;
use App\Formatter\RandaFormatter;
use App\Repository\RanaRepository;
use App\Repository\UserRepository;
use App\Repository\RandaRepository;
use App\Repository\ChapterRepository;
use App\Repository\DirectorRepository;
use App\Repository\NewMemberRepository;
use App\Repository\RetentionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\OldDB\Repository\RegionRepository;
use App\Repository\RanaLifecycleRepository;
use App\Repository\RenewedMemberRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        RegionRepository $regionRepository
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
            }
        }
        if ($c_date) {
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

        file_put_contents("pippo", "randa exists", FILE_APPEND);

        if ($randa) {

            $timeslot = $randa->getCurrentTimeslot();
            file_put_contents("pippo", $timeslot . "\n", FILE_APPEND);
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

        $nexttimeslot = "T" . (substr($timeslot, -1) + 1);
        if ($randa) {
            $randa->setCurrentState("APPR");
            $randa->setCurrentTimeslot($nexttimeslot);
            $randa->setDirectorsPrevisions($directors);
            $randa->setNote($note);
            $this->randaRepository->save($randa);
        }


        $chapters = $this->chapterRepository->findBy([
            "region" => $region
        ]);

        foreach ($chapters as $chapter) {
            $rana = new Rana();
            $rana->setChapter($chapter);
            $rana->setRanda($randa);
            $this->ranaRepository->save($rana);

            $ranaLifeCycle = new RanaLifecycle();
            $ranaLifeCycle->setCurrentState(Constants::RANA_LIFECYCLE_STATUS_TODO);
            $ranaLifeCycle->setCurrentTimeslot(Constants::TIMESLOT_T0);
            $ranaLifeCycle->setRana($rana);
            $this->ranaLifecycleRepository->save($ranaLifeCycle);
            $this->entityManager->refresh($rana);
        }

        $this->randaRepository->save($randa);
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
            $randa->setCurrentState("TODO");
            $randa->setRefuseNote($refuseNote);
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

            $randa->setCurrentTimeslot($nextTimeslot);
            $randa->setCurrentState("TODO");

            // file_put_contents("pippo", $timeslot . "\n", FILE_APPEND);
            // file_put_contents("pippo", $slotNumber . "\n", FILE_APPEND);
            // file_put_contents("pippo", $nextSlotNumber . "\n", FILE_APPEND);
            // file_put_contents("pippo", $nextTimeslot . "\n", FILE_APPEND);

            $this->randaRepository->save($randa);

            $chapters = $this->chapterRepository->findBy([
                "region" => $region
            ]);
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

                $ranaLifeCycle = new RanaLifecycle();
                $ranaLifeCycle->setCurrentState("TODO");
                $ranaLifeCycle->setCurrentTimeslot($nextTimeslot);
                $ranaLifeCycle->setRana($rana);
                $this->ranaLifecycleRepository->save($ranaLifeCycle);
                $this->entityManager->refresh($rana);
            }

            return new JsonResponse(true);
        } catch (Exception $e) {
            file_put_contents("pippo", $e->getMessage());
            return new JsonResponse(false);
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

                $chapters = $this->chapterRepository->findBy([
                    "currentState" => "CORE_GROUP",
                    "region" => $region
                ]);

                $data["core_groups_new"] = [];
                $data["core_groups_ret"] = [];
                $data["core_groups_act"] = [];
                $data["chapters_new"] = [];
                $data["chapters_ret"] = [];
                $data["chapters_act"] = [];
                $data["chapters_average"] = [];
                $data["core_groups_average"] = [];
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

                $core_groups = $this->chapterRepository->findBy([
                    "region" => $region,
                    "currentState" => "CORE_GROUP"
                ]);

                foreach ($core_groups as $core_group) {

                    $year = $core_group->getPrevLaunchChapterDate()->format("Y");
                    $initial_members = $core_group->getMembers();
                    if ($year == (int) date("Y")) {

                        $chapter_element_cg_ret = [
                            "name" => $core_group->getName(),
                            "data" => []
                        ];
                        $chapter_element_cg_new = [
                            "name" => $core_group->getName(),
                            "data" => []
                        ];
                        $chapter_element_cg_act = [
                            "name" => $core_group->getName(),
                            "data" => []
                        ];
                        $chapter_element_ret = [
                            "name" => $core_group->getName(),
                            "data" => []
                        ];
                        $chapter_element_act = [
                            "name" => $core_group->getName(),
                            "data" => []
                        ];

                        $chapter_element_new = [
                            "name" => $core_group->getName(),
                            "data" => []
                        ];


                        $month = $core_group->getPrevLaunchChapterDate()->format("m");
                        $rana = $this->ranaRepository->findOneBy([
                            "chapter" => $core_group,
                            "randa" => $randa
                        ]);

                        $new = $this->newMemberRepository->findOneBy([
                            "rana" => $rana,
                            "timeslot" => $timeslot,
                            "valueType" => "APPR"
                        ]);
                        $ret = $this->retentionRepository->findOneBy([
                            "rana" => $rana,
                            "timeslot" => $timeslot,
                            "valueType" => "APPR"
                        ]);
                        $new_members = 0;
                        $ret_members = 0;
                        for ($i = 1; $i <= 12; $i++) {
                            $method = "getM$i";
                            $new_members += $new->$method();
                            $ret_members +=  $ret->$method();
                            if ($i % 3 == 0) {


                                // è ancora un core group
                                if ($i < $month) {
                                    $chapter_element_cg_new["data"][] = $new_members;
                                    $chapter_element_cg_ret["data"][] = $ret_members;
                                    $cg_len = sizeof($chapter_element_cg_act["data"]);

                                    $initial_cg_members = 0;
                                    if ($core_group->getActualLaunchCoregroupDate()->format("Y") == $currentYear - 1) {
                                        $prev_randa = $this->randaRepository->findOneBy([
                                            "region" => $region,
                                            "year" => $core_group->getActualLaunchCoregroupDate()->format("Y")
                                        ]);
                                        if ($prev_randa) {
                                            $prev_rana = $this->ranaRepository->findOneBy([
                                                "randa" => $prev_randa,
                                                "chapter" => $core_group
                                            ]);
                                            if ($prev_rana) {
                                                $cg_ret_dec = $this->retentionRepository->findOneBy([
                                                    "rana" => $prev_rana,
                                                    "valueType" => "CONS",
                                                    "timeslot" => "T4"
                                                ]);
                                                if ($cg_ret_dec) {
                                                    $r = $cg_ret_dec->getM12();
                                                }
                                                $cg_new_dec = $this->newMemberRepository->findOneBy([
                                                    "rana" => $prev_rana,
                                                    "valueType" => "CONS",
                                                    "timeslot" => "T4"
                                                ]);
                                                if ($cg_new_dec) {
                                                    $n = $cg_new_dec->getM12();
                                                }
                                                if ($r && $n) {
                                                    $initial_cg_members = $core_group->getMembers() + ($n - $r);
                                                }
                                            }
                                        }
                                    }

                                    $chapter_element_cg_act["data"][] = ($cg_len ? $chapter_element_cg_act["data"][$cg_len - 1] : $initial_cg_members) + ($new_members - $ret_members);

                                    $chapter_element_new["data"][] = null;
                                    $chapter_element_ret["data"][] = null;
                                    $chapter_element_act["data"][] = null;
                                } else {
                                    $chapter_element_new["data"][] = $new_members;
                                    $chapter_element_ret["data"][] = $ret_members;
                                    $act_len = sizeof($chapter_element_act["data"]);
                                    $chapter_element_act["data"][] = ($act_len ? $chapter_element_act["data"][$act_len - 1] : $initial_members) + ($new_members - $ret_members);

                                    $slot = ceil($i / 3) - 1;
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
                        "data" => []
                    ];
                    $chapter_element_act = [
                        "name" => $chapter->getName(),
                        "data" => []
                    ];

                    $chapter_element_new = [
                        "name" => $chapter->getName(),
                        "data" => []
                    ];

                    $rana = $this->ranaRepository->findOneBy([
                        "chapter" => $chapter,
                        "randa" => $randa
                    ]);

                    $new = $this->newMemberRepository->findOneBy([
                        "rana" => $rana,
                        "timeslot" => $timeslot,
                        "valueType" => "APPR"
                    ]);
                    $ret = $this->retentionRepository->findOneBy([
                        "rana" => $rana,
                        "timeslot" => $timeslot,
                        "valueType" => "APPR"
                    ]);

                    $new_members = 0;
                    $ret_members = 0;
                    for ($i = 1; $i <= 12; $i++) {
                        $method = "getM$i";
                        $new_members += $new->$method();
                        $ret_members +=  $ret->$method();
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

                $projects = $this->chapterRepository->findBy([
                    "region" => $region,
                    "currentState" => "PROJECT"
                ]);

                foreach ($projects as $project) {
                    $year = $project->getPrevLaunchChapterDate()->format("Y");
                    $initial_members = $project->getMembers();
                    if ($year == (int) date("Y")) {

                        $chapter_element_cg_ret = [
                            "name" => $project->getName(),
                            "data" => []
                        ];
                        $chapter_element_cg_new = [
                            "name" => $project->getName(),
                            "data" => []
                        ];
                        $chapter_element_cg_act = [
                            "name" => $project->getName(),
                            "data" => []
                        ];
                        $chapter_element_ret = [
                            "name" => $project->getName(),
                            "data" => []
                        ];
                        $chapter_element_act = [
                            "name" => $project->getName(),
                            "data" => []
                        ];

                        $chapter_element_new = [
                            "name" => $project->getName(),
                            "data" => []
                        ];

                        $month_chapter = $project->getPrevLaunchChapterDate()->format("m");
                        $month_core_group = $project->getPrevLaunchCoregroupDate()->format("m");

                        $rana = $this->ranaRepository->findOneBy([
                            "chapter" => $project,
                            "randa" => $randa
                        ]);

                        $new = $this->newMemberRepository->findOneBy([
                            "rana" => $rana,
                            "timeslot" => $timeslot,
                            "valueType" => "APPR"
                        ]);
                        $ret = $this->retentionRepository->findOneBy([
                            "rana" => $rana,
                            "timeslot" => $timeslot,
                            "valueType" => "APPR"
                        ]);
                        $new_members = 0;
                        $ret_members = 0;
                        for ($i = 1; $i <= 12; $i++) {
                            $method = "getM$i";
                            $new_members += $new->$method();
                            $ret_members +=  $ret->$method();
                            if ($i % 3 == 0) {

                                if ($i >= $month_chapter) {
                                    $chapter_element_new["data"][] = $new_members;
                                    $chapter_element_ret["data"][] = $ret_members;
                                    $act_len = sizeof($chapter_element_act["data"]);
                                    $chapter_element_act["data"][] = ($act_len ? $chapter_element_act["data"][$act_len - 1] : $initial_members) + ($new_members - $ret_members);

                                    $slot = ceil($i / 3) - 1;
                                    $data["num_chapters"][$slot]++;

                                    $chapter_element_cg_act["data"][] = null;
                                    $chapter_element_cg_new["data"][] = null;
                                    $chapter_element_cg_ret["data"][] = null;
                                } else if ($i >= $month_core_group) {
                                    $chapter_element_cg_new["data"][] = $new_members;
                                    $chapter_element_cg_ret["data"][] = $ret_members;
                                    $cg_len = sizeof($chapter_element_cg_act["data"]);
                                    $chapter_element_cg_act["data"][] = ($cg_len ? $chapter_element_cg_act["data"][$cg_len - 1] : 0) + ($new_members - $ret_members);

                                    $chapter_element_new["data"][] = null;
                                    $chapter_element_ret["data"][] = null;
                                    $chapter_element_act["data"][] = null;
                                } else {
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
                        $data["core_groups_act"][] = $chapter_element_act;
                        $data["chapters_new"][] = $chapter_element_new;
                        $data["chapters_ret"][] = $chapter_element_ret;
                        $data["chapters_act"][] = $chapter_element_act;
                    }
                }

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
                for ($i = 0; $i < 4; $i++) {
                    $data["chapters_average"][$i] = 0;
                    if (sizeof($data["chapters_act"])) {
                        $data["chapters_average"][$i] = round($chapter_sums[$i] / sizeof($data["chapters_act"]));
                    }
                    $data["core_groups_average"][$i] = 0;
                    if (sizeof($data["core_groups_act"])) {
                        $data["core_groups_average"][$i] = round($chapter_sums[$i] / sizeof($data["core_groups_act"]));
                    }
                }

                $directors = $randa->getDirectorsPrevisions() ? $randa->getDirectorsPrevisions() : "0,0,0,0";
                $data["directors"] = explode(",", $directors);
                $data["randa_state"] = $randa->getCurrentState();

                return new JsonResponse($data);
            } else {
                return new JsonResponse(false);
            }
        } catch (Exception $e) {
            file_put_contents("pippo", $e->getMessage());
            return new JsonResponse(false);
        }
    }
}
