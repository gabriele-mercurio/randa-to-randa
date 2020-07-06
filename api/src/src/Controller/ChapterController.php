<?php

namespace App\Controller;

use Exception;
use App\Util\Util;
use App\Entity\Region;
use App\Entity\Chapter;
use App\Util\Constants;
use App\Entity\Director;
use Swagger\Annotations as SWG;
use App\Repository\RanaRepository;
use App\Repository\UserRepository;
use App\Formatter\ChapterFormatter;
use App\Repository\RandaRepository;
use App\Repository\ChapterRepository;
use App\Repository\DirectorRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RanaLifecycleRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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


    /** @var RanaRepository */
    private $ranaRepository;

    public function __construct(
        ChapterFormatter $chapterFormatter,
        ChapterRepository $chapterRepository,
        DirectorRepository $directorRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        RanaLifecycleRepository $ranaLifecycleRepository,
        RandaRepository $randaRepository,
        RanaRepository $ranaRepository
    ) {
        $this->chapterFormatter = $chapterFormatter;
        $this->chapterRepository = $chapterRepository;
        $this->directorRepository = $directorRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->ranaLifecycleRepository = $ranaLifecycleRepository;
        $this->randaRepository = $randaRepository;
        $this->ranaRepository = $ranaRepository;
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
            $actualLaunchCoregroupDate = $request->get("actualLaunchCoregroupDate");
            $prevLaunchChapterDate = $request->get("prevLaunchChapterDate");
            $actualLaunchChapterDate = $request->get("actualLaunchChapterDate");

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
            try {
                if (!is_null($prevLaunchCoregroupDate)) {
                    $prevLaunchCoregroupDate = Util::UTCDateTime($prevLaunchCoregroupDate);
                }
                if (!is_null($actualLaunchCoregroupDate)) {
                    $actualLaunchCoregroupDate = Util::UTCDateTime($actualLaunchCoregroupDate);
                }
            } catch (Exception $ex) {
                $errorFields['launchCoregroupDate'] = "invalid";
            }

            if (!is_null($prevLaunchCoregroupDate) && !is_null($actualLaunchCoregroupDate)) {
                $errorFields['launchCoregroupDate'] = "invalid";
            } else {
                if (!is_null($prevLaunchCoregroupDate)) {
                    if ($prevLaunchCoregroupDate < $today) {
                        $actualLaunchCoregroupDate = $prevLaunchCoregroupDate;
                        $prevLaunchCoregroupDate = null;
                        $state = Constants::CHAPTER_STATE_CORE_GROUP;
                    } else {
                        $state = Constants::CHAPTER_STATE_PROJECT;
                    }
                }

                if (!is_null($actualLaunchCoregroupDate)) {
                    if ($actualLaunchCoregroupDate >= $today) {
                        $prevLaunchCoregroupDate = $actualLaunchCoregroupDate;
                        $actualLaunchCoregroupDate = null;
                        $state = Constants::CHAPTER_STATE_PROJECT;
                    } else {
                        $state = Constants::CHAPTER_STATE_CORE_GROUP;
                    }
                }
            }

            // Chapter dates
            try {
                if (!is_null($prevLaunchChapterDate)) {
                    $prevLaunchChapterDate = Util::UTCDateTime($prevLaunchChapterDate);
                }
                if (!is_null($actualLaunchChapterDate)) {
                    $actualLaunchChapterDate = Util::UTCDateTime($actualLaunchChapterDate);
                }
            } catch (Exception $ex) {
                $errorFields['launchChapterDate'] = "invalid";
            }

            if (!is_null($prevLaunchChapterDate) && !is_null($actualLaunchChapterDate)) {
                $errorFields['launchChapterDate'] = "invalid";
            } else {
                if (!is_null($prevLaunchChapterDate)) {
                    if ($prevLaunchChapterDate < $today) {
                        $actualLaunchChapterDate = $prevLaunchChapterDate;
                        $prevLaunchChapterDate = null;
                        $previuosState = $state;
                        $state = Constants::CHAPTER_STATE_CHAPTER;
                    }
                }


                if (!is_null($actualLaunchChapterDate)) {
                    if ($actualLaunchChapterDate >= $today) {
                        $prevLaunchChapterDate = $actualLaunchChapterDate;
                        $actualLaunchChapterDate = null;
                    } else {
                        $state = is_null($previuosState) ? $state : $previuosState;
                    }
                }
            }

            // Date constraints
            $coregroupDate = $prevLaunchCoregroupDate ? $prevLaunchCoregroupDate : $actualLaunchCoregroupDate;
            $chapterDate = $prevLaunchChapterDate ? $prevLaunchChapterDate : $actualLaunchChapterDate;
            if (!is_null($coregroupDate) && !is_null($chapterDate) && $chapterDate < $coregroupDate) {
                $errorFields['launchChapterDate'] = "invalid";
                $errorFields['launchCoregroupDate'] = "invalid";
            }

            if (in_array($state, [
                Constants::CHAPTER_STATE_PROJECT,
                Constants::CHAPTER_STATE_CORE_GROUP
            ]) && is_null($prevLaunchChapterDate)) {
                $errorFields['launchChapterDate'] = "required";
            }

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

            $chapter = new Chapter();
            $chapter->setActualLaunchChapterDate($actualLaunchChapterDate);
            $chapter->setActualLaunchCoregroupDate($actualLaunchCoregroupDate);
            $chapter->setCurrentState($state);
            $chapter->setDirector($d);
            $chapter->setName($name);
            $chapter->setPrevLaunchChapterDate($prevLaunchChapterDate);
            $chapter->setPrevLaunchCoregroupDate($prevLaunchCoregroupDate);
            $chapter->setRegion($region);
            $this->chapterRepository->save($chapter);

            return new JsonResponse($this->chapterFormatter->formatFull($chapter), Response::HTTP_CREATED);
        } else {
            $errorFields = $code == Response::HTTP_BAD_REQUEST ? $errorFields : null;
            return new JsonResponse($errorFields, $code);
        }
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
            $chapterDirector = $request->get("director");
            $members = $request->get("members");
            $prevLaunchCoregroupDate = $request->get("prevLaunchCoregroupDate");
            $prevLaunchChapterDate = $request->get("prevLaunchChapterDate");
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
            if (!empty($chapterDirector)) {
                $chapterDirector = $this->userRepository->find(trim($chapterDirector));
                if (is_null($chapterDirector)) {
                    $errorFields['director'] = "invalid";
                } else {
                    $fields['director'] = $chapterDirector;
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

            $state = $chapter->getCurrentState();
            // Check Coregroup and chapter dates
            switch ($chapter->getCurrentState()) {
                case Constants::CHAPTER_STATE_PROJECT:
                    if (!empty($prevLaunchCoregroupDate)) {
                        $prevLaunchCoregroupDate = trim($prevLaunchCoregroupDate);
                        if (!empty($prevLaunchCoregroupDate)) {
                            try {
                                $prevLaunchCoregroupDate = Util::UTCDateTime($prevLaunchCoregroupDate);
                            } catch (Exception $ex) {
                                $errorFields['prevLaunchCoregroupDate'] = "invalid";
                            }

                            if (!array_key_exists('prevLaunchCoregroupDate', $errorFields)) {
                                if ($prevLaunchCoregroupDate <= $today) {
                                    $errorFields['prevLaunchCoregroupDate'] = "invalid";
                                } else {
                                    $fields['prevLaunchCoregroupDate'] = $prevLaunchCoregroupDate;
                                }
                            }
                        }
                    }
                case Constants::CHAPTER_STATE_CORE_GROUP:
                    if (!empty($prevLaunchChapterDate)) {
                        $prevLaunchChapterDate = trim($prevLaunchChapterDate);
                        if (!empty($prevLaunchChapterDate)) {
                            try {
                                $prevLaunchChapterDate = Util::UTCDateTime($prevLaunchChapterDate);
                            } catch (Exception $ex) {
                                $errorFields['prevLaunchChapterDate'] = "invalid";
                            }

                            if (!array_key_exists('prevLaunchChapterDate', $errorFields)) {
                                if ($prevLaunchChapterDate <= $today) {
                                    $errorFields['prevLaunchChapterDate'] = "invalid";
                                } else {
                                    $fields['prevLaunchChapterDate'] = $prevLaunchChapterDate;
                                }
                            }
                        }
                    }
            }

            // Resume date
            if (!empty($prevResumeDate)) {
                $prevResumeDate = trim($prevResumeDate);
                if (!empty($prevResumeDate)) {
                    try {
                        $prevResumeDate = Util::UTCDateTime($prevResumeDate);
                    } catch (Exception $ex) {
                        $errorFields['resumeDate'] = "invalid";
                    }

                    if (!array_key_exists('resumeDate', $errorFields)) {
                        if ($prevResumeDate <= $today) {
                            $errorFields['prevResumeDate'] = "invalid";
                        } else {
                            $fields['prevResumeDate'] = $prevResumeDate;
                        }
                    }
                }
            }

            if (in_array($state, [
                Constants::CHAPTER_STATE_PROJECT,
                Constants::CHAPTER_STATE_CORE_GROUP
            ]) && is_null($prevLaunchChapterDate)) {
                $errorFields['launchChapterDate'] = "empty";
            }

            if (!empty($errorFields)) {
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

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

        if ($code == Response::HTTP_OK) {
            foreach ($fields as $key => $value) {
                switch ($key) {
                    case 'name':
                        $chapter->setName($value);
                        break;
                    case 'director':
                        $d = $this->directorRepository->findOneBy([
                            'user' => $value,
                            'region' => $region,
                            'role' => Constants::ROLE_ASSISTANT
                        ]);

                        if (is_null($d)) {
                            $d = new Director();
                            $d->setRegion($region);
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
        } else {
            $errorFields = $code == Response::HTTP_BAD_REQUEST ? $errorFields : null;
            return new JsonResponse($errorFields, $code);
        }
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
        ]);

        $timeslot = $randa->getCurrentTimeslot();

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


            $chapters_data["chapters"] = array_map(function ($c) use ($randa) {
                $today = Util::UTCDateTime();
                $warning = null;

                if (is_null($c->getActualLaunchCoregroupDate()) && $c->getPrevLaunchCoregroupDate() <= $today) {
                    $warning = "CORE_GROUP";
                } elseif (is_null($c->getActualLaunchChapterDate()) && $c->getPrevLaunchChapterDate() <= $today) {
                    $warning = "CHAPTER";
                }

                $ret = $this->chapterFormatter->formatWithStatus($c, $randa);
                $ret['warning'] = $warning;
                return $ret;
            }, $chapters);

            $chapters_data["randa"] = [
                "state" => $randa->getCurrentState(),
                "timeslot" => $randa->getCurrentTimeslot(),
                "year" => $currentYear
            ];

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
            ], [
                'currentTimeslot' => 'DESC'
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
            $today = Util::UTCDateTime();

            $chapter->setActualLaunchChapterDate($today);
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
            $today = Util::UTCDateTime();

            $chapter->setActualLaunchCoregroupDate($today);
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
