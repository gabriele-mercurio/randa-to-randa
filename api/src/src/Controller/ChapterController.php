<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Director;
use App\Entity\Region;
use App\Formatter\ChapterFormatter;
use App\Formatter\DirectorFormatter;
use App\Formatter\RegionFormatter;
use App\Repository\ChapterRepository;
use App\Repository\DirectorRepository;
use App\Repository\UserRepository;
use App\Util\Util;
use DateTime;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChapterController extends AbstractController
{
    /** @var ChapterFormatter */
    private $chapterFormatter;

    /** @var ChapterRepository */
    private $chapterRepository;

    /** @var DirectorFormatter */
    private $directorFormatter;

    /** @var DirectorRepository */
    private $directorRepository;

    /** @var RegionFormatter */
    private $regionFormatter;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(
        ChapterFormatter $chapterFormatter,
        ChapterRepository $chapterRepository,
        DirectorFormatter $directorFormatter,
        DirectorRepository $directorRepository,
        RegionFormatter $regionFormatter,
        UserRepository $userRepository
    ) {
        $this->chapterFormatter = $chapterFormatter;
        $this->chapterRepository = $chapterRepository;
        $this->directorFormatter = $directorFormatter;
        $this->directorRepository = $directorRepository;
        $this->regionFormatter = $regionFormatter;
        $this->userRepository = $userRepository;
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
        $actAs = $request->get("actAs");
        $code = Response::HTTP_OK;
        $role = $request->get("role");
        $user = $this->getUser();

        $checkUser = $this->userRepository->checkUser($user, $actAs);
        $user = Util::arrayGetValue($checkUser, 'user');
        $code = Util::arrayGetValue($checkUser, 'code');

        if ($code == Response::HTTP_OK && !is_null($role) && !in_array($role, [
            $this->directorRepository::DIRECTOR_ROLE_AREA,
            $this->directorRepository::DIRECTOR_ROLE_ASSISTANT,
            $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE
        ])) {
            $code = Response::HTTP_BAD_REQUEST;
        }

        if ($code == Response::HTTP_OK) {
            $checkDirectorRole = $this->directorRepository->checkDirectorRole($user, $region, $role);

            $code = Util::arrayGetValue($checkDirectorRole, 'code', $code);
            $director = Util::arrayGetValue($checkDirectorRole, 'director', null);
        }

        if ($code == Response::HTTP_OK) {
            $role = $director->getRole();

            switch ($role) {
                case $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE:
                    $chapters = $this->chapterRepository->findBy([
                        'region' => $region
                    ]);
                break;
                case $this->directorRepository::DIRECTOR_ROLE_AREA:
                    $directors = [
                        $director->getId() => $director
                    ];
                    foreach ($this->directorRepository->findBy([
                        'supervisor' => $director
                    ]) as $d) {
                        $id = $d->getId();
                        if (!array_key_exists($id, $directors)) {
                            $directors[$id] = $d;
                        }
                    }
                    $directors = array_values($directors);
                    $chapters = [];

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
                case $this->directorRepository::DIRECTOR_ROLE_ASSISTANT:
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

            return new JsonResponse(array_map(function ($c) {
                $today = new DateTime();
                $warning = null;

                if (is_null($c->getActualLaunchCoregroupDate()) && $c->getPrevLaunchCoregroupDate() <= $today) {
                    $warning = "CORE_GROUP";
                } elseif (is_null($c->getActualLaunchChatperDate()) && $c->getPrevLaunchChatperDate() <= $today) {
                    $warning = "CHAPTER";
                }

                $ret = $this->chapterFormatter->formatBase($c);
                $ret['warning'] = $warning;
                return $ret;
            }, $chapters));
        } else {
            return new JsonResponse(null, $code);
        }
    }

    /**
     * Get chapters
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
     *          type="object",
     *          @SWG\Property(
     *              property="fields",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(property="field_name", type="string", description="The type of the error; possible values are 'empty', 'in_use' or 'invalid'")
     *              )
     *          )
     *      )
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin or if the user is not an admin and he/she (or the emulated user) has not ecetuve director rigths."
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
        $actAsId = $request->get("actAs");
        $code = Response::HTTP_OK;
        $fields = [];
        $user = $this->getUser();

        $checkUser = $this->userRepository->checkUser($user, $actAsId);
        $actAs = Util::arrayGetValue($checkUser, 'user');
        $code = Util::arrayGetValue($checkUser, 'code');

        if ($code == Response::HTTP_OK && (!$user->isAdmin() || !is_null($actAsId))) {
            $u = is_null($actAsId) ? $user : $actAs;
            $director = $this->directorRepository->findOneBy([
                'user' => $u,
                'region' => $region,
                'role' => $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE
            ]);

            if (is_null($director)) {
                $code = Response::HTTP_FORBIDDEN;
            }
        }

        if ($code == Response::HTTP_OK) {
            $name = $request->get("name", "");
            $chapterDirector = $request->get("director");
            $prevLaunchCoregroupDate = $request->get("prevLaunchCoregroupDate");
            $actualLaunchCoregroupDate = $request->get("actualLaunchCoregroupDate");
            $prevLaunchChapterDate = $request->get("prevLaunchChapterDate");
            $actualLaunchChapterDate = $request->get("actualLaunchChapterDate");

            $previuosState = null;
            $state = $this->chapterRepository::CHAPTER_CURRENT_STATE_PROJECT;
            $today = Util::UTCDateTime();

            if (empty($name)) {
                $fields['name'] = "empty";
            } elseif ($this->chapterRepository->findOneBy([
                'name' => $name,
                'region' => $region
            ])) {
                $fields['name'] = "in_use";
            }

            if (empty($chapterDirector)) {
                $fields['director'] = "empty";
            } elseif (is_null($this->userRepository->find($chapterDirector))) {
                $fields['director'] = "invalid";
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
                $fields['launchCoregroupDate'] = "invalid";
            }

            if (!is_null($prevLaunchCoregroupDate) && !is_null($actualLaunchCoregroupDate)) {
                $fields['launchCoregroupDate'] = "invalid";
            } else {
                if (!is_null($prevLaunchCoregroupDate) && $prevLaunchCoregroupDate < $today) {
                    $actualLaunchCoregroupDate = is_null($actualLaunchCoregroupDate) ? $prevLaunchCoregroupDate : $actualLaunchCoregroupDate;
                    $prevLaunchCoregroupDate = null;
                    $state = $this->chapterRepository::CHAPTER_CURRENT_STATE_CORE_GROUP;
                }

                if (!is_null($actualLaunchCoregroupDate) && $actualLaunchCoregroupDate >= $today) {
                    $prevLaunchCoregroupDate = $actualLaunchCoregroupDate;
                    $actualLaunchCoregroupDate = null;
                    $state = $this->chapterRepository::CHAPTER_CURRENT_STATE_PROJECT;
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
                $code = Response::HTTP_BAD_REQUEST;
                $fields['launchChapterDate'] = "invalid";
            }

            if (!is_null($prevLaunchChapterDate) && !is_null($actualLaunchChapterDate)) {
                $fields['launchChapterDate'] = "invalid";
            } else {
                if (!is_null($prevLaunchChapterDate) && $prevLaunchChapterDate < $today) {
                    $actualLaunchChapterDate = is_null($actualLaunchChapterDate) ? $prevLaunchChapterDate : $actualLaunchChapterDate;
                    $prevLaunchChapterDate = null;
                    $previuosState = $state;
                    $state = $this->chapterRepository::CHAPTER_CURRENT_STATE_CHAPTER;
                }

                if (!is_null($actualLaunchChapterDate) && $actualLaunchChapterDate >= $today) {
                    $prevLaunchChapterDate = $actualLaunchChapterDate;
                    $actualLaunchChapterDate = null;
                    $state = is_null($previuosState) ? $state : $previuosState;
                }
            }

            // Date constraints
            $coregroupDate = $prevLaunchCoregroupDate ? $prevLaunchCoregroupDate : $actualLaunchCoregroupDate;
            $chapterDate = $prevLaunchChapterDate ? $prevLaunchChapterDate : $actualLaunchChapterDate;
            if (!is_null($coregroupDate) && !is_null($chapterDate) && $chapterDate < $coregroupDate) {
                $fields['launchChapterDate'] = "invalid";
                $fields['launchCoregroupDate'] = "invalid";
            }

            if (in_array($state, [
                $this->chapterRepository::CHAPTER_CURRENT_STATE_PROJECT,
                $this->chapterRepository::CHAPTER_CURRENT_STATE_CORE_GROUP
            ]) && is_null($prevLaunchChapterDate)) {
                $fields['launchChapterDate'] = "empty";
            }

            if (!empty($fields)) {
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

        if ($code == Response::HTTP_OK) {
            $d = $this->directorRepository->findOneBy([
                'user' => $chapterDirector,
                'region' => $region,
                'role' => $this->directorRepository::DIRECTOR_ROLE_ASSISTANT
            ]);

            if (is_null($d)) {
                $d = new Director();
                $d->setRegion($region);
                $d->setRole($this->directorRepository::DIRECTOR_ROLE_ASSISTANT);
                $d->setUser($chapterDirector);
                $this->directorRepository->save($d);
            }

            $chapter = new Chapter();
            $chapter->setActualLaunchChatperDate($actualLaunchChapterDate);
            $chapter->setActualLaunchCoregroupDate($actualLaunchCoregroupDate);
            $chapter->setCurrentState($state);
            $chapter->setDirector($d);
            $chapter->setName($name);
            $chapter->setPrevLaunchChatperDate($prevLaunchChapterDate);
            $chapter->setPrevLaunchCoregroupDate($prevLaunchCoregroupDate);
            $chapter->setRegion($region);
            $this->chapterRepository->save($chapter);

            return new JsonResponse($this->chapterFormatter->formatFull($chapter), Response::HTTP_CREATED);
        } else {
            $fields = $code == Response::HTTP_BAD_REQUEST ? $fields : null;
            return new JsonResponse($fields, $code);
        }
    }
}
