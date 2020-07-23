<?php

namespace App\Controller;

use Exception;
use App\Util\Util;
use App\Entity\Rana;
use App\Entity\Randa;
use App\Entity\Region;
use App\Entity\Chapter;
use App\Util\Constants;
use App\Entity\Director;
use App\Entity\NewMember;
use App\Entity\Retention;
use App\Entity\RanaLifecycle;
use Swagger\Annotations as SWG;
use App\Repository\RanaRepository;
use App\Repository\UserRepository;
use App\Formatter\ChapterFormatter;
use App\Repository\RandaRepository;
use App\Repository\ChapterRepository;
use App\Repository\DirectorRepository;
use App\Repository\NewMemberRepository;
use App\Repository\RetentionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RanaLifecycleRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



/**
* @Route("/api")
**/
class ChapterController extends AbstractController
{
    /** @var ChapterFormatter */
    private $chapterFormatter;

    /** @var ChapterRepository */
    private $chapterRepository;

    /** @var DirectorRepository */
    private $directorRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserRepository */
    private $userRepository;

    /** @var RanaLifecycleRepository */
    private $ranaLifecycleRepository;

    /** @var RandaRepository */
    private $randaRepository;

    /** @var NewMemberRepository */
    private $newMemberRepository;

    /** @var RetentionRepository */
    private $retentionRepository;


    /** @var RanaRepository */
    private $ranaRepository;

    /** @var RanaRepository */
    private $retention;


    public function __construct(
        ChapterFormatter $chapterFormatter,
        ChapterRepository $chapterRepository,
        DirectorRepository $directorRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        RanaLifecycleRepository $ranaLifecycleRepository,
        RandaRepository $randaRepository,
        RanaRepository $ranaRepository,
        RetentionRepository $retentionRepository,
        NewMemberRepository $newMemberRepository
    ) {
        $this->chapterFormatter = $chapterFormatter;
        $this->chapterRepository = $chapterRepository;
        $this->directorRepository = $directorRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->ranaLifecycleRepository = $ranaLifecycleRepository;
        $this->randaRepository = $randaRepository;
        $this->ranaRepository = $ranaRepository;
        $this->newMemberRepository = $newMemberRepository;
        $this->retentionRepository = $retentionRepository;
    }

    /**
     * Close the Chapter
     *
     * @Route(path="/chapter/{id}/close", name="close_chapter", methods={"PUT"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The chapter"
     * )
     * @SWG\Parameter(
     *      name="role",
     *      in="query",
     *      type="string",
     *      description="Optional parameter to get data relative to the specified given role"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="query",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns a Chapter object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="chapterLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *          @SWG\Property(
     *              property="coreGroupLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *          @SWG\Property(
     *              property="director",
     *              type="object",
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="integer")
     *          ),
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(property="members", type="integer"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(
     *              property="resume",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="suspDate", type="string", description="Suspension date"),
     *          @SWG\Property(property="warning", type="string", description="Available values: NULL, 'CORE_GROUP' or 'CHAPTER'")
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if role is given but is not valid."
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin, if a valid role is given but the user has not that role for the specified region or the role is not enought for the operation."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function closeChapter(Chapter $chapter, Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $region = $chapter->getRegion();

        $roleCheck = [
            Constants::ROLE_EXECUTIVE,
            Constants::ROLE_NATIONAL
        ];
        $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
        }

        if ($code == Response::HTTP_OK) {
            if ($chapter->getCurrentState() == Constants::CHAPTER_STATE_CLOSED) {
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

        if ($code == Response::HTTP_OK) {
            $today = Util::UTCDateTime();

            $chapter->setClosureDate($today);
            $chapter->setCurrentState(Constants::CHAPTER_STATE_CLOSED);
            $this->entityManager->flush();

            return new JsonResponse($this->chapterFormatter->formatBase($chapter));
        } else {
            return new JsonResponse(null, $code);
        }
    }

    /**
     * Create a chapter
     * Only admin users or EXECUTIVE directors for the specified region can create a chapter
     *
     * @Route(path="/{id}/chapter", name="create_chapter", methods={"POST"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The region"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="formData",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Parameter(
     *      name="name",
     *      in="formData",
     *      type="string",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="director",
     *      in="formData",
     *      type="string",
     *      description="The user id of the designated chapter director",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="prevLaunchCoregroupDate",
     *      in="formData",
     *      type="string",
     *      description="Optional previsioning coregroup launch date."
     * )
     * @SWG\Parameter(
     *      name="actualLaunchCoregroupDate",
     *      in="formData",
     *      type="string",
     *      description="Optional actual coregroup launch date. If this date is given prevLaunchCoregroupDate is not given."
     * )
     * @SWG\Parameter(
     *      name="prevLaunchChapterDate",
     *      in="formData",
     *      type="string",
     *      description="Optional previsioning chapter launch date."
     * )
     * @SWG\Parameter(
     *      name="actualLaunchChapterDate",
     *      in="formData",
     *      type="string",
     *      description="Optional actual chapter launch date. If this date is given prevLaunchChapterDate is not given."
     * )
     * @SWG\Response(
     *      response=201,
     *      description="Returns a Chapter object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="chapterLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *          @SWG\Property(
     *              property="coreGroupLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *          @SWG\Property(
     *              property="director",
     *              type="object",
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="integer")
     *          ),
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(property="members", type="integer"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(
     *              property="resume",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="suspDate", type="string", description="Suspension date"),
     *          @SWG\Property(property="warning", type="string", description="Available values: NULL, 'CORE_GROUP' or 'CHAPTER'")
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if name is not given, director is not given or is not valid, one or more date are not well formed or if the chapter must be created in project or coregroup state and the previsioning launch chapter date is not given.",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="field_name", type="string", description="The type of the error; possible values are 'required', 'in_use' or 'invalid'")
     *          )
     *      )
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin or if the user is not an admin and he/she (or the emulated user) has not excetuve director rigths."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function createChapter(Region $region, Request $request): Response
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

        $errorFields = [];

        if ($request->get("isFreeAccount")) {
            $code = Response::HTTP_OK;
        }

        if ($code == Response::HTTP_OK) {

            $name = trim($request->get("name", ""));
            $chapterDirector = $request->get("director");
            $prevLaunchCoregroupDate = $request->get("prevLaunchCoregroupDate");
            $prevLaunchChapterDate = $request->get("prevLaunchChapterDate");

            $previuosState = null;
            $state = Constants::CHAPTER_STATE_PROJECT;
            $today = Util::UTCDateTime();


            if (empty($name)) {
                $errorFields['name'] = "required";
            } elseif ($this->chapterRepository->findOneBy([
                'name' => $name,
                'region' => $region
            ])) {
                $errorFields['name'] = "in_use";
            }

            if (empty($chapterDirector)) {
                $errorFields['director'] = "required";
            }
            $chapterDirector = $this->userRepository->find($chapterDirector);
            if (is_null($chapterDirector)) {
                $errorFields['director'] = "invalid";
            }

            // Coregroup dates
            // try {
            //     if (!is_null($prevLaunchCoregroupDate)) {
            //         $prevLaunchCoregroupDate = Util::UTCDateTime($prevLaunchCoregroupDate);
            //     }
            //     if (!is_null($actualLaunchCoregroupDate)) {
            //         $actualLaunchCoregroupDate = Util::UTCDateTime($actualLaunchCoregroupDate);
            //     }
            // } catch (Exception $ex) {
            //     $errorFields['launchCoregroupDate'] = "invalid";
            // }

            // if (!is_null($prevLaunchCoregroupDate) && !is_null($actualLaunchCoregroupDate)) {
            //     $errorFields['launchCoregroupDate'] = "invalid";
            // } else {
            //     if (!is_null($prevLaunchCoregroupDate)) {
            //         if ($prevLaunchCoregroupDate < $today) {
            //             $actualLaunchCoregroupDate = $prevLaunchCoregroupDate;
            //             $prevLaunchCoregroupDate = null;
            //             $state = Constants::CHAPTER_STATE_CORE_GROUP;
            //         } else {
            //             $state = Constants::CHAPTER_STATE_PROJECT;
            //         }
            //     }

            //     if (!is_null($actualLaunchCoregroupDate)) {
            //         if ($actualLaunchCoregroupDate >= $today) {
            //             $prevLaunchCoregroupDate = $actualLaunchCoregroupDate;
            //             $actualLaunchCoregroupDate = null;
            //             $state = Constants::CHAPTER_STATE_PROJECT;
            //         } else {
            //             $state = Constants::CHAPTER_STATE_CORE_GROUP;
            //         }
            //     }
            // }

            // // Chapter dates
            // try {
            //     if (!is_null($prevLaunchChapterDate)) {
            //         $prevLaunchChapterDate = Util::UTCDateTime($prevLaunchChapterDate);
            //     }
            //     if (!is_null($actualLaunchChapterDate)) {
            //         $actualLaunchChapterDate = Util::UTCDateTime($actualLaunchChapterDate);
            //     }
            // } catch (Exception $ex) {
            //     $errorFields['launchChapterDate'] = "invalid";
            // }

            // if (!is_null($prevLaunchChapterDate) && !is_null($actualLaunchChapterDate)) {
            //     $errorFields['launchChapterDate'] = "invalid";
            // } else {
            //     if (!is_null($prevLaunchChapterDate)) {
            //         if ($prevLaunchChapterDate < $today) {
            //             $actualLaunchChapterDate = $prevLaunchChapterDate;
            //             $prevLaunchChapterDate = null;
            //             $previuosState = $state;
            //             $state = Constants::CHAPTER_STATE_CHAPTER;
            //         }
            //     }


            //     if (!is_null($actualLaunchChapterDate)) {
            //         if ($actualLaunchChapterDate >= $today) {
            //             $prevLaunchChapterDate = $actualLaunchChapterDate;
            //             $actualLaunchChapterDate = null;
            //         } else {
            //             $state = is_null($previuosState) ? $state : $previuosState;
            //         }
            //     }
            // }

            // // Date constraints
            $coregroupDate = $prevLaunchCoregroupDate ? $prevLaunchCoregroupDate : $actualLaunchCoregroupDate;
            $chapterDate = $prevLaunchChapterDate ? $prevLaunchChapterDate : $actualLaunchChapterDate;
            // if (!is_null($coregroupDate) && !is_null($chapterDate) && $chapterDate < $coregroupDate) {
            //     $errorFields['launchChapterDate'] = "invalid";
            //     $errorFields['launchCoregroupDate'] = "invalid";
            // }

            // if (in_array($state, [
            //     Constants::CHAPTER_STATE_PROJECT,
            //     Constants::CHAPTER_STATE_CORE_GROUP
            // ]) && is_null($prevLaunchChapterDate)) {
            //     $errorFields['launchChapterDate'] = "required";
            // }

            if (!empty($errorFields)) {
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

        if ($code == Response::HTTP_OK) {

            $d = $this->directorRepository->findOneBy([
                'user' => $chapterDirector,
                'region' => $region
                //'role' => Constants::ROLE_ASSISTANT
            ]);

            if (is_null($d)) {
                $d = new Director();
                $d->setRegion($region);
                $d->setRole(Constants::ROLE_ASSISTANT);
                $d->setUser($chapterDirector);
                $this->directorRepository->save($d);
            }


            $prevLaunchChapterDate = $prevLaunchChapterDate ? Util::UTCDateTime($prevLaunchChapterDate) : null;
            $prevLaunchCoregroupDate = $prevLaunchCoregroupDate ? Util::UTCDateTime($prevLaunchCoregroupDate) : null;

            // $coreGroupLaunch = $actualLaunchCoregroupDate ?? $prevLaunchCoregroupDate;
            // $chapterLaunch = $actualLaunchChapterDate ?? $prevLaunchChapterDate;

            $state = "PROJECT";
            $today =  Util::UTCDateTime();

          
            $chapter = new Chapter();
            $chapter->setDirector($d);
            $chapter->setName($name);
            $chapter->setPrevLaunchChapterDate($prevLaunchChapterDate);
            $chapter->setPrevLaunchCoregroupDate($prevLaunchCoregroupDate);
            $chapter->setRegion($region);
            $chapter->setMembers(0);
            $chapter->setCurrentState($state);

            $this->chapterRepository->save($chapter);

            $currentYear = (int) date("Y");
            $randa = $this->randaRepository->findOneBy([
                "region" => $region,
                "year" => $currentYear
            ]);

            if (!$randa) {
                $randa = new Randa();
                $randa->setYear($currentYear);
                $timeslot = Util::getTimeslotFromCurrentMonth();
                $randa->setCurrentTimeslot($timeslot);
                $randa->setCurrentState("TODO");
                $randa->setRegion($region);
                $this->randaRepository->save($randa);
            }
            $randa_timeslot = $randa->getCurrentTimeslot();

            $rana = new Rana();
            $rana->setChapter($chapter);
            $rana->setRanda($randa);
            $this->ranaRepository->save($rana);

            file_put_contents("chapter_log", "Chapter name: " . $chapter->getName() . "\n", FILE_APPEND);
            file_put_contents("chapter_log", "RANA ID: " . $rana->getId() . "\n", FILE_APPEND);

            $return_values = ChapterController::initializeRetentionsAndNewMembers($randa_timeslot, $rana);
            $this->retentionRepository->save($return_values[0]);
            $this->retentionRepository->save($return_values[1]);
            $this->newMemberRepository->save($return_values[2]);
            $this->newMemberRepository->save($return_values[3]);
            $this->ranaLifecycleRepository->save($return_values[4]);

            file_put_contents("chapter_log", "Retentions and new members initialized...\n", FILE_APPEND);

            if (isset($return_values[5]) && isset($return_values[6]) && isset($return_values[7])) {
                $this->retentionRepository->save($return_values[5]);
                $this->newMemberRepository->save($return_values[6]);
                $this->ranaLifecycleRepository->save($return_values[7]);
                file_put_contents("chapter_log", "T is grater than 0, so i set the t - 1 values...\n", FILE_APPEND);
            }

            return new JsonResponse($this->chapterFormatter->formatFull($chapter), Response::HTTP_CREATED);
        } else {
            $errorFields = $code == Response::HTTP_BAD_REQUEST ? $errorFields : null;
            return new JsonResponse($errorFields, $code);
        }
    }

    public static function initializeRetentionsAndNewMembers($timeslot, $rana)
    {
        $numeric_timeslot = (int) substr($timeslot, -1);

        $retentions_cons = new Retention();
        $retentions_cons->setRana($rana);
        $retentions_cons->setValueType("CONS");
        $retentions_cons->setTimeslot("T0");

        $new_members_cons = new NewMember();
        $new_members_cons->setRana($rana);
        $new_members_cons->setValueType("CONS");
        $new_members_cons->setTimeslot("T0");

        $retentions = new Retention();
        $retentions->setRana($rana);
        $retentions->setValueType("TODO");
        $retentions->setTimeslot($timeslot);

        $new_members = new NewMember();
        $new_members->setRana($rana);
        $new_members->setValueType("TODO");
        $new_members->setTimeslot($timeslot);

        $rana_lifecycle = new RanaLifecycle();
        $rana_lifecycle->setRana($rana);
        $rana_lifecycle->setCurrentTimeslot($timeslot);
        $rana_lifecycle->setCurrentState("TODO");


        if ($numeric_timeslot > 0) {
            $timeslot_prev = "T" . ($numeric_timeslot - 1);
            $retentions_prev = new Retention();
            $retentions_prev->setRana($rana);
            $retentions_prev->setValueType("APPR");
            $retentions_prev->setTimeslot($timeslot_prev);

            $new_members_prev = new NewMember();
            $new_members_prev->setRana($rana);
            $new_members_prev->setValueType("APPR");
            $new_members_prev->setTimeslot($timeslot_prev);

            $rana_lifecycle_prev = new RanaLifecycle();
            $rana_lifecycle_prev->setRana($rana);
            $rana_lifecycle_prev->setCurrentTimeslot($timeslot_prev);
            $rana_lifecycle_prev->setCurrentState("APPR");
        }

        $current_month = date("m");
        for ($i = 1; $i <= 12; $i++) {
            if ($i < $current_month) {
                $retentions_cons->setMonth($i, 0);
                $new_members_cons->setMonth($i, 0);
            } else {
                $retentions_cons->setMonth($i, null);
                $new_members_cons->setMonth($i, null);
            }
            $retentions->setMonth($i, 0);
            $new_members->setMonth($i, 0);

            if ($numeric_timeslot > 0) {
                $retentions_prev->setMonth($i, 0);
                $new_members_prev->setMonth($i, 0);
            }
        }

        $return_values = [
            $retentions,
            $retentions_cons,
            $new_members,
            $new_members_cons,
            $rana_lifecycle
        ];
        if ($numeric_timeslot > 0) {
            $return_values = array_merge($return_values, [$retentions_prev, $new_members_prev, $rana_lifecycle_prev]);
        }
        return $return_values;
    }

    /**
     * Edit a chapter
     * Canges to members and prevResumeDate can be made from any authorized directors; for all other fields the user must be admin or have EXECUTIVE role.
     * Changes to launch date fields from prev to actual are not allowed: there are specific API calls to launch a coregroup or a chapter, use them instead
     *
     * @Route(path="/chapter/{id}", name="edit_chapter", methods={"PUT"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The chapter"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="formData",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Parameter(
     *      name="name",
     *      in="formData",
     *      type="string",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="director",
     *      in="formData",
     *      type="string",
     *      description="The user id of the designated chapter director",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="members",
     *      in="formData",
     *      type="string",
     *      description="Optional previsioning coregroup launch date."
     * )
     * @SWG\Parameter(
     *      name="prevLaunchCoregroupDate",
     *      in="formData",
     *      type="string",
     *      description="Optional actual coregroup launch date. If this date is given prevLaunchCoregroupDate is not given."
     * )
     * @SWG\Parameter(
     *      name="prevLaunchChapterDate",
     *      in="formData",
     *      type="string",
     *      description="Optional previsioning chapter launch date."
     * )
     * @SWG\Parameter(
     *      name="prevResumeDate",
     *      in="formData",
     *      type="string",
     *      description="Optional previsioning chapter resume date."
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns a Chapter object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="chapterLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *          @SWG\Property(
     *              property="coreGroupLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *          @SWG\Property(
     *              property="director",
     *              type="object",
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="integer")
     *          ),
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(property="members", type="integer"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(
     *              property="resume",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="suspDate", type="string", description="Suspension date"),
     *          @SWG\Property(property="warning", type="string", description="Available values: NULL, 'CORE_GROUP' or 'CHAPTER'")
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if some data check errors are found.",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="field_name", type="string", description="The type of the error; possible values are 'required', 'in_use' or 'invalid'")
     *          )
     *      )
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if the user or the emulated user are not authorized to meke the requested changes."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function editChapter(Chapter $chapter, Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $region = $chapter->getRegion();

        $roleCheck = [
            Constants::ROLE_EXECUTIVE,
            Constants::ROLE_AREA,
            Constants::ROLE_ASSISTANT
        ];
        $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
        }

        if ($code == Response::HTTP_OK) {
            $name = $request->get("name");
            $chapterUser = $request->get("director");
            $members = $request->get("members");

            $prevLaunchCoregroupDate = $request->get("prevLaunchCoregroupDate");
            //$actualLaunchCoregroupDate = $request->get("actualLaunchCoregroupDate");
            $prevLaunchChapterDate = $request->get("prevLaunchChapterDate");
            //$actualLaunchChapterDate = $request->get("actualLaunchChapterDate");


            $prevResumeDate = $request->get("prevResumeDate");

            $errorFields = $fields = [];
            $today = Util::UTCDateTime();

            // Check Name
            if (!empty($name)) {
                $name = trim($name);
                if (empty($name)) {
                    $errorFields['name'] = "required";
                } elseif ($this->chapterRepository->existsOtherWithSameFields($chapter, [
                    'name' => $name,
                    'region' => $region
                ])) {
                    $errorFields['name'] = "in_use";
                } else {
                    $fields['name'] = $name;
                }
            }

            // Check Chapter Director
            if (!empty($chapterUser)) {

                $chapterDirector = $this->directorRepository->findOneBy([
                    "user" => $chapterUser
                ]);
                if (is_null($chapterDirector)) {
                    $errorFields['director'] = "invalid";
                } else {
                    $fields['director'] = $chapterDirector->getUser();
                }
            }

            // Check Members
            if (!empty($members)) {
                $members = (int) $members;
                if (is_nan($members) || $members < 0) {
                    $errorFields['members'] = "invalid";
                } else {
                    $fields['members'] = $members;
                }
            }

            // Check Coregroup and chapter dates
            // switch ($chapter->getCurrentState()) {
            //     case Constants::CHAPTER_STATE_PROJECT:
            //         if (!empty($prevLaunchCoregroupDate)) {
            //             $prevLaunchCoregroupDate = trim($prevLaunchCoregroupDate);
            //             if (!empty($prevLaunchCoregroupDate)) {
            //                 try {
            //                     $prevLaunchCoregroupDate = Util::UTCDateTime($prevLaunchCoregroupDate);
            //                 } catch (Exception $ex) {
            //                     $errorFields['prevLaunchCoregroupDate'] = "invalid";
            //                 }

            //                 if (!array_key_exists('prevLaunchCoregroupDate', $errorFields)) {
            //                     if ($prevLaunchCoregroupDate <= $today) {
            //                         $errorFields['prevLaunchCoregroupDate'] = "invalid";
            //                     } else {
            //                         $fields['prevLaunchCoregroupDate'] = $prevLaunchCoregroupDate;
            //                     }
            //                 }
            //             }
            //         }
            //     case Constants::CHAPTER_STATE_CORE_GROUP:
            //         if (!empty($prevLaunchChapterDate)) {
            //             $prevLaunchChapterDate = trim($prevLaunchChapterDate);
            //             if (!empty($prevLaunchChapterDate)) {
            //                 try {
            //                     $prevLaunchChapterDate = Util::UTCDateTime($prevLaunchChapterDate);
            //                 } catch (Exception $ex) {
            //                     $errorFields['prevLaunchChapterDate'] = "invalid";
            //                 }

            //                 if (!array_key_exists('prevLaunchChapterDate', $errorFields)) {
            //                     if ($prevLaunchChapterDate <= $today) {
            //                         $errorFields['prevLaunchChapterDate'] = "invalid";
            //                     } else {
            //                         $fields['prevLaunchChapterDate'] = $prevLaunchChapterDate;
            //                     }
            //                 }
            //             }
            //         }
            // }

            // Resume date
            // if (!empty($prevResumeDate)) {
            //     $prevResumeDate = trim($prevResumeDate);
            //     if (!empty($prevResumeDate)) {
            //         try {
            //             $prevResumeDate = Util::UTCDateTime($prevResumeDate);
            //         } catch (Exception $ex) {
            //             $errorFields['resumeDate'] = "invalid";
            //         }

            //         if (!array_key_exists('resumeDate', $errorFields)) {
            //             if ($prevResumeDate <= $today) {
            //                 $errorFields['prevResumeDate'] = "invalid";
            //             } else {
            //                 $fields['prevResumeDate'] = $prevResumeDate;
            //             }
            //         }
            //     }
            // }

            // if (in_array($state, [
            //     Constants::CHAPTER_STATE_PROJECT,
            //     Constants::CHAPTER_STATE_CORE_GROUP
            // ]) && is_null($prevLaunchChapterDate)) {
            //     $errorFields['launchChapterDate'] = "empty";
            // }

            // if (!empty($errorFields)) {
            //     $code = Response::HTTP_BAD_REQUEST;
            // }
        }

        //$actualLaunchChapterDate = $actualLaunchChapterDate ? Util::UTCDateTime($actualLaunchChapterDate) : null;
        //$actualLaunchCoregroupDate = $actualLaunchCoregroupDate ? Util::UTCDateTime($actualLaunchCoregroupDate) : null;
        $prevLaunchChapterDate = $prevLaunchChapterDate ? Util::UTCDateTime($prevLaunchChapterDate) : null;
        $prevLaunchCoregroupDate = $prevLaunchCoregroupDate ? Util::UTCDateTime($prevLaunchCoregroupDate) : null;

        // if ($coreGroupLaunch) {
        //     if ($coreGroupLaunch < $today) {
        //         $state = "CORE_GROUP";
        //     }
        // }
        // if ($chapterLaunch) {
        //     if ($chapterLaunch < $today) {
        //         $state = "CHAPTER";
        //     }
        // }
        $chapter->setPrevLaunchChapterDate($prevLaunchChapterDate);
        $chapter->setPrevLaunchCoregroupDate($prevLaunchCoregroupDate);

        if ($code == Response::HTTP_OK) {
            $keys = array_keys($fields);
            $protectedKeys = array_filter($keys, function ($key) {
                return in_array($key, [
                    'director',
                    'name',
                    'prevLaunchChapterDate',
                    'prevLaunchCoregroupDate'
                ]);
            });

            if (!empty($protectedKeys) && $role != Constants::ROLE_EXECUTIVE) {
                $code = Response::HTTP_FORBIDDEN;
            }
        }

        foreach ($fields as $key => $value) {
            switch ($key) {
                case 'name':
                    $chapter->setName($value);
                    break;
                case 'director':
                    $d = $chapterDirector;
                    // $d = $this->directorRepository->findOneBy([
                    //     'user' => $value,
                    //     'region' => $region,
                    //     'role' => Constants::ROLE_ASSISTANT
                    // ]);

                    if (is_null($d)) {
                        $d = new Director();
                        $d->setRegion($region);
                        $d->setFreeAccount(false);
                        $d->setRole(Constants::ROLE_ASSISTANT);
                        $d->setUser($value);
                        $this->directorRepository->save($d);
                    }

                    $chapter->setDirector($d);
                    break;
                case 'members':
                    $chapter->setMembers($value);
                    break;
                case 'prevLaunchCoregroupDate':
                    $chapter->setPrevLaunchCoregroupDate($value);
                    break;
                case 'prevLaunchChapterDate':
                    $chapter->setPrevLaunchChapterDate($value);
                    break;
                case 'prevResumeDate':
                    $chapter->setPrevResumeDate($value);
                    break;
            }
        }

        $this->entityManager->flush();

        return new JsonResponse($this->chapterFormatter->formatFull($chapter), Response::HTTP_CREATED);
    }


    /**
     * Edit a chapter
     * Canges to members and prevResumeDate can be made from any authorized directors; for all other fields the user must be admin or have EXECUTIVE role.
     * Changes to launch date fields from prev to actual are not allowed: there are specific API calls to launch a coregroup or a chapter, use them instead
     *
     * @Route(path="/chapter/freeAccount", name="free_account", methods={"GET"})
     *
     * @return Response
     */
    public function freeAccountChapter(Request $request): Response
    {
        $user = $this->getUser();
        $director = $this->directorRepository->findOneBy([
            "user" => $user
        ]);
        if ($director) {
            if ($director->isFreeAccount()) {
                $chapter = $this->chapterRepository->findOneBy([
                    "director" => $director
                ]);
                if (!$chapter) {
                    $code = Response::HTTP_NOT_FOUND;
                    return new JsonResponse("Chapter not found", $code);
                } else {
                    return new JsonResponse($this->chapterFormatter->formatBase($chapter));
                }
            } else {
                $code = Response::HTTP_FORBIDDEN;
                return new JsonResponse("Not free account", $code);
            }
            return new JsonResponse($director->isFreeAccount(), Response::HTTP_CREATED);
        } else {
            $code = Response::HTTP_NOT_FOUND;
            return new JsonResponse("", $code);
        }
    }



    /**
     * Get chapters
     *
     * @Route(path="/chapter/{id}", name="chapter_detail", methods={"GET"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The chapter"
     * )
     * @SWG\Parameter(
     *      name="role",
     *      in="query",
     *      type="string",
     *      description="Optional parameter to get data relative to the specified given role"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="query",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns a Chapter object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="chapterLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *          @SWG\Property(
     *              property="coreGroupLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *          @SWG\Property(
     *              property="director",
     *              type="object",
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="integer")
     *          ),
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(property="members", type="integer"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(
     *              property="resume",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="suspDate", type="string", description="Suspension date"),
     *          @SWG\Property(property="warning", type="string", description="Available values: NULL, 'CORE_GROUP' or 'CHAPTER'")
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if role is given but is not valid."
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin or if a valid role is given but the user has not that role for the specified region."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function getChapter(Chapter $chapter, Request $request): Response
    {
        $region = $chapter->getRegion();

        $roleCheck = [
            Constants::ROLE_EXECUTIVE,
            Constants::ROLE_AREA,
            Constants::ROLE_ASSISTANT
        ];
        $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
        }

        if ($code == Response::HTTP_OK) {
            return new JsonResponse($this->chapterFormatter->formatBase($chapter));
        } else {
            return new JsonResponse(null, $code);
        }
    }

    /**
     * Get chapters
     *
     * @Route(path="/{id}/chapters", name="chapters_list", methods={"GET"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The region"
     * )
     * @SWG\Parameter(
     *      name="role",
     *      in="query",
     *      type="string",
     *      description="Optional parameter to get data relative to the specified given role"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="query",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns an array of Chapter objects",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(
     *                  property="chapterLaunch",
     *                  type="object",
     *                  @SWG\Property(property="actual", type="string", description="Actual date"),
     *                  @SWG\Property(property="prev", type="string", description="Expected date")
     *              ),
     *              @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *              @SWG\Property(
     *                  property="coreGroupLaunch",
     *                  type="object",
     *                  @SWG\Property(property="actual", type="string", description="Actual date"),
     *                  @SWG\Property(property="prev", type="string", description="Expected date")
     *              ),
     *              @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *              @SWG\Property(
     *                  property="director",
     *                  type="object",
     *                  @SWG\Property(property="fullName", type="string"),
     *                  @SWG\Property(property="id", type="integer")
     *              ),
     *              @SWG\Property(property="id", type="string"),
     *              @SWG\Property(property="members", type="integer"),
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(
     *                  property="resume",
     *                  type="object",
     *                  @SWG\Property(property="actual", type="string", description="Actual date"),
     *                  @SWG\Property(property="prev", type="string", description="Expected date")
     *              ),
     *              @SWG\Property(property="suspDate", type="string", description="Suspension date"),
     *              @SWG\Property(property="warning", type="string", description="Available values: NULL, 'CORE_GROUP' or 'CHAPTER'")
     *          )
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if role is given but is not valid."
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin or if a valid role is given but the user has not that role for the specified region."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function getChapters(Region $region, Request $request): Response
    {

        $roleCheck = [
            Constants::ROLE_EXECUTIVE,
            Constants::ROLE_AREA,
            Constants::ROLE_ASSISTANT
        ];
        $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
        }

        $currentYear = (int) date("Y");

        $randa = $this->randaRepository->findOneBy([
            "region" => $region,
            "year" => $currentYear
        ], [
            "currentTimeslot" => "DESC"
        ]);


        header("role: ". $role);

        if ($code == Response::HTTP_OK) {
            switch ($role) {
                case Constants::ROLE_EXECUTIVE:

                    $chapters = $this->chapterRepository->findBy([
                        'region' => $region
                    ]);
                    break;
                case Constants::ROLE_AREA:
                    $directors = $chapters = [];
                    $directors[$director->getId()] = $director;
                    foreach ($this->directorRepository->findBy([
                        'supervisor' => $director
                    ]) as $d) {
                        $id = $d->getId();
                        if (!array_key_exists($id, $directors)) {
                            $directors[$id] = $d;
                        }
                    }
                    $directors = array_values($directors);

                    foreach ($directors as $d) {
                        foreach ($this->chapterRepository->findBy([
                            'director' => $d,
                            'region' => $region
                        ]) as $c) {
                            $id = $c->getId();
                            if (!array_key_exists($id, $chapters)) {
                                $chapters[$id] = $c;
                            }
                        }
                    }
                    $chapters = array_values($chapters);
                    break;
                case Constants::ROLE_ASSISTANT:
                    $chapters = $this->chapterRepository->findBy([
                        'director' => $director,
                        'region' => $region
                    ]);

                    break;
            }
            usort($chapters, function ($c1, $c2) {
                $name1 = $c1->getName();
                $name2 = $c2->getName();
                return $name1 < $name2 ? -1 : ($name1 > $name2 ? 1 : 0);
            });

            foreach ($chapters as $chapter) {
                $rana = $this->ranaRepository->findOneBy([
                    "chapter" => $chapter,
                    "randa" => $randa
                ]);
                if ($rana) {

                    $members = $chapter->getMembers();


                    $new_members_cons = $this->newMemberRepository->findOneBy([
                        "rana" => $rana,
                        "valueType" => "CONS",
                        "timeslot" => "T0"
                    ]);
                    $retentions_cons = $this->retentionRepository->findOneBy([
                        "rana" => $rana,
                        "valueType" => "CONS",
                        "timeslot" => "T0"
                    ]);

                    if($new_members_cons && $retentions_cons) {
                        $last_number_new = 0;
                        for ($i = 1; $i <= 12; $i++) {
                            $method = "getM$i";
                            $cons = $new_members_cons->$method();
                            if ($cons !== null) {
                                $last_number_new = $i;
                            }
                        }
    
                        $new_members_ts = [];
                        for ($i = 1; $i <= 12; $i++) {
                            $method = "getM$i";
                            $val = 0;
                            if ($i <= $last_number_new) {
                                $val = $new_members_cons->$method();
                            } else {
                                $val = 0;
                            }
                            $new_members_ts[] = $val;
                        }
    
    
                      
    
                        $last_number_ret = 0;
                        for ($i = 1; $i <= 12; $i++) {
                            $method = "getM$i";
                            $cons = $retentions_cons->$method();
                            if ($cons !== null) {
                                $last_number_ret = $i;
                            }
                        }
    
                        $retentions_ts = [];
                        for ($i = 1; $i <= 12; $i++) {
                            $method = "getM$i";
                            $val = 0;
                            if ($i <= $last_number_ret) {
                                $val = $retentions_cons->$method();
                            } else {
                                $val = 0;
                            }
                            $retentions_ts[] = $val;
                        }
    
    
                        for ($i = 0; $i < $last_number_ret; $i++) {
                            $members += ($new_members_ts[$i] - $retentions_ts[$i]);
                        }
                    }
                    
                    $chapter->setMembers($members);
                }
            }


            $chapters_data["chapters"] = array_map(function ($c) use ($randa) {
                $today = Util::UTCDateTime();
                $warning = null;

                if (is_null($c->getActualLaunchCoregroupDate()) && $c->getPrevLaunchCoregroupDate() <= $today) {
                    $warning = "CORE_GROUP";
                } elseif (is_null($c->getActualLaunchChapterDate()) && $c->getPrevLaunchChapterDate() <= $today) {
                    $warning = "CHAPTER";
                }


                if ($randa) {

                    $rana = $this->ranaRepository->findOneBy([
                        "chapter" => $c,
                        "randa" => $randa
                    ]);
                    if ($rana) {
                        $lifecycle = $this->ranaLifecycleRepository->findOneBy([
                            "rana" => $rana,
                            "currentTimeslot" => $randa->getCurrentTimeslot()
                        ]);
                        if ($lifecycle) {

                            $state = $lifecycle->getCurrentState();
                        } else {
                            $state = "TODO";
                        }
                    } else {
                        $state = "TODO";
                    }
                } else {
                    $state = "TODO";
                }
                $ret = $this->chapterFormatter->formatWithStatus($c, $state);
                $ret['warning'] = $warning;
                return $ret;
            }, $chapters);


            if ($randa) {
                $chapters_data["randa"] = [
                    "state" => $randa->getCurrentState(),
                    "timeslot" => $randa->getCurrentTimeslot(),
                    "year" => $currentYear,
                    "refuse_note" => $randa->getRefuseNote(),
                    "directors_previsions" => $randa->getDirectorsPrevisions()
                ];
            }
            return new JsonResponse($chapters_data);
        } else {
            return new JsonResponse(null, $code);
        }
    }

    /**
     * Get chapters
     *
     * @Route(path="/{id}/chapters-statistics", name="chapters_stats", methods={"GET"})
     *
     */
    public function chaptersStatistics(Region $region, Request $request): Response
    {
        $roleCheck = [
            Constants::ROLE_EXECUTIVE,
            Constants::ROLE_AREA,
            Constants::ROLE_ASSISTANT
        ];
        $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
        }

        $currentYear = (int) date("Y");

        if ($code == Response::HTTP_OK) {

            $data = [
                "todo" => [],
                "proposed" => [],
                "approved" => []
            ];

            $chapters = $this->chapterRepository->findBy([
                "region" => $region
            ]);
            $randa = $this->randaRepository->findOneBy([
                'year' => $currentYear,
                'region' => $region
            ]);

            $timeslot = $randa->getCurrentTimeslot();

            $all_approved = true;
            foreach ($chapters as $chapter) {
                $rana = $this->ranaRepository->findOneBy([
                    "chapter" => $chapter,
                    "randa" => $randa
                ]);
                if (!$rana) {
                    $data["todo"][] = $chapter;
                    $all_approved = false;
                } else {
                    $lifecycles = $this->ranaLifecycleRepository->findBy([
                        "rana" => $rana,
                        "currentTimeslot" => $timeslot
                    ]);
                    foreach ($lifecycles as $lifecycle) {
                        switch ($lifecycle->getCurrentState()) {
                            case "TODO":
                                $data["todo"][] = $this->chapterFormatter->formatBase($chapter);
                                $all_approved = false;
                                break;
                            case "PROP":
                                $data["proposed"][] = $this->chapterFormatter->formatBase($chapter);
                                $all_approved = false;
                                break;
                            case "APPR":
                                $data["approved"][] = $this->chapterFormatter->formatBase($chapter);
                                $all_approved = false;
                                break;
                        }
                    }
                }
            }

            $data["all_approved"] = $all_approved;
            $data["randa_timeslot"] = $timeslot;
            $data["randa_state"] = $randa->getCurrentState();
            $data["randa_refuse_note"] = $randa->getRefuseNote();
            return new JsonResponse($data, $code);
        } else {
            return new JsonResponse(null, $code);
        }
    }

    /**
     * Launch the Chapter
     *
     * @Route(path="/chapter/{id}/launch", name="launch_chapter", methods={"PUT"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The chapter"
     * )
     * @SWG\Parameter(
     *      name="role",
     *      in="query",
     *      type="string",
     *      description="Optional parameter to get data relative to the specified given role"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="query",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns a Chapter object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="chapterLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *          @SWG\Property(
     *              property="coreGroupLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *          @SWG\Property(
     *              property="director",
     *              type="object",
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="integer")
     *          ),
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(property="members", type="integer"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(
     *              property="resume",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="suspDate", type="string", description="Suspension date"),
     *          @SWG\Property(property="warning", type="string", description="Available values: NULL, 'CORE_GROUP' or 'CHAPTER'")
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if role is given but is not valid."
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin, if a valid role is given but the user has not that role for the specified region or the role is not enought for the operation."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function launchChapter(Chapter $chapter, Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $region = $chapter->getRegion();
        $date = $request->get("date");

        $roleCheck = [
            Constants::ROLE_EXECUTIVE,
            Constants::ROLE_NATIONAL
        ];
        $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
        }

        if ($code == Response::HTTP_OK) {
            if ($chapter->getCurrentState() != Constants::CHAPTER_STATE_CORE_GROUP) {
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

        if ($code == Response::HTTP_OK) {
            $date = Util::UTCDateTime($date);

            $chapter->setActualLaunchChapterDate($date);
            $chapter->setCurrentState(Constants::CHAPTER_STATE_CHAPTER);
            $this->entityManager->flush();

            return new JsonResponse($this->chapterFormatter->formatBase($chapter));
        } else {
            return new JsonResponse(null, $code);
        }
    }

    /**
     * Launch the CoreGroup
     *
     * @Route(path="/chapter/{id}/launch-coregroup", name="launch_coregroup", methods={"PUT"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The chapter"
     * )
     * @SWG\Parameter(
     *      name="role",
     *      in="query",
     *      type="string",
     *      description="Optional parameter to get data relative to the specified given role"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="query",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns a Chapter object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="chapterLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *          @SWG\Property(
     *              property="coreGroupLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *          @SWG\Property(
     *              property="director",
     *              type="object",
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="integer")
     *          ),
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(property="members", type="integer"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(
     *              property="resume",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="suspDate", type="string", description="Suspension date"),
     *          @SWG\Property(property="warning", type="string", description="Available values: NULL, 'CORE_GROUP' or 'CHAPTER'")
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if role is given but is not valid."
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin, if a valid role is given but the user has not that role for the specified region or the role is not enought for the operation."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function launchCoreGroup(Chapter $chapter, Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $region = $chapter->getRegion();
        $date = $request->get("date");

        $roleCheck = [
            Constants::ROLE_EXECUTIVE,
            Constants::ROLE_NATIONAL
        ];
        $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
        }

        if ($code == Response::HTTP_OK) {
            if ($chapter->getCurrentState() != Constants::CHAPTER_STATE_PROJECT) {
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

        if ($code == Response::HTTP_OK) {
            $date = Util::UTCDateTime($date);

            $chapter->setActualLaunchCoregroupDate($date);
            $chapter->setCurrentState(Constants::CHAPTER_STATE_CORE_GROUP);
            $this->entityManager->flush();

            return new JsonResponse($this->chapterFormatter->formatBase($chapter));
        } else {
            return new JsonResponse(null, $code);
        }
    }

    /**
     * Resume the Chapter
     *
     * @Route(path="/chapter/{id}/resume", name="resume_chapter", methods={"PUT"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The chapter"
     * )
     * @SWG\Parameter(
     *      name="role",
     *      in="query",
     *      type="string",
     *      description="Optional parameter to get data relative to the specified given role"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="query",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns a Chapter object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="chapterLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *          @SWG\Property(
     *              property="coreGroupLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *          @SWG\Property(
     *              property="director",
     *              type="object",
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="integer")
     *          ),
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(property="members", type="integer"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(
     *              property="resume",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="suspDate", type="string", description="Suspension date"),
     *          @SWG\Property(property="warning", type="string", description="Available values: NULL, 'CORE_GROUP' or 'CHAPTER'")
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if role is given but is not valid."
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin, if a valid role is given but the user has not that role for the specified region or the role is not enought for the operation."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function resumeChapter(Chapter $chapter, Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $region = $chapter->getRegion();

        $roleCheck = [
            Constants::ROLE_EXECUTIVE,
            Constants::ROLE_NATIONAL
        ];
        $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
        }

        if ($code == Response::HTTP_OK) {
            if ($chapter->getCurrentState() != Constants::CHAPTER_STATE_SUSPENDED) {
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

        if ($code == Response::HTTP_OK) {
            $today = Util::UTCDateTime();

            $chapter->setActualResumeDate($today);
            $chapter->setCurrentState(Constants::CHAPTER_STATE_CHAPTER);
            $this->entityManager->flush();

            return new JsonResponse($this->chapterFormatter->formatBase($chapter));
        } else {
            return new JsonResponse(null, $code);
        }
    }

    /**
     * Suspend the Chapter
     *
     * @Route(path="/chapter/{id}/suspend", name="suspend_chapter", methods={"PUT"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The chapter"
     * )
     * @SWG\Parameter(
     *      name="role",
     *      in="query",
     *      type="string",
     *      description="Optional parameter to get data relative to the specified given role"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="query",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns a Chapter object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="chapterLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *          @SWG\Property(
     *              property="coreGroupLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *          @SWG\Property(
     *              property="director",
     *              type="object",
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="integer")
     *          ),
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(property="members", type="integer"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(
     *              property="resume",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="suspDate", type="string", description="Suspension date"),
     *          @SWG\Property(property="warning", type="string", description="Available values: NULL, 'CORE_GROUP' or 'CHAPTER'")
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if role is given but is not valid."
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin, if a valid role is given but the user has not that role for the specified region or the role is not enought for the operation."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function suspendChapter(Chapter $chapter, Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $region = $chapter->getRegion();

        $roleCheck = [
            Constants::ROLE_EXECUTIVE,
            Constants::ROLE_NATIONAL
        ];
        $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
        }

        if ($code == Response::HTTP_OK) {
            if ($chapter->getCurrentState() != Constants::CHAPTER_STATE_CHAPTER) {
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

        if ($code == Response::HTTP_OK) {
            $today = Util::UTCDateTime();

            $chapter->setSuspDate($today);
            $chapter->setCurrentState(Constants::CHAPTER_STATE_SUSPENDED);
            $this->entityManager->flush();

            return new JsonResponse($this->chapterFormatter->formatBase($chapter));
        } else {
            return new JsonResponse(null, $code);
        }
    }
}
