<?php

namespace App\Controller;

use App\Entity\Region;
use App\Formatter\ChapterFormatter;
use App\Formatter\DirectorFormatter;
use App\Formatter\RegionFormatter;
use App\Repository\ChapterRepository;
use App\Repository\DirectorRepository;
use App\Util\Util;
use DateTime;
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

    public function __construct(
        ChapterFormatter $chapterFormatter,
        ChapterRepository $chapterRepository,
        DirectorFormatter $directorFormatter,
        DirectorRepository $directorRepository,
        RegionFormatter $regionFormatter
    ) {
        $this->chapterFormatter = $chapterFormatter;
        $this->chapterRepository = $chapterRepository;
        $this->directorFormatter = $directorFormatter;
        $this->directorRepository = $directorRepository;
        $this->regionFormatter = $regionFormatter;
    }

    /**
     * Get chapters
     *
     * @Route(path="/{id}/chapters", name="chapters_list", methods={"GET"})
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The region"
     * )
     * @SWG\Parameter(
     *     name="role",
     *     in="query",
     *     type="string",
     *     description="Optional parameter to get data relative to the specified given role"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns an array of Chapter objects",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *             type="object",
     *             @SWG\Property(
     *                 property="chapterLaunch",
     *                 type="object",
     *                 @SWG\Property(property="actual", type="string", description="Actual date"),
     *                 @SWG\Property(property="prev", type="string", description="Expected date")
     *             ),
     *             @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *             @SWG\Property(
     *                 property="coreGroupLaunch",
     *                 type="object",
     *                 @SWG\Property(property="actual", type="string", description="Actual date"),
     *                 @SWG\Property(property="prev", type="string", description="Expected date")
     *             ),
     *             @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *             @SWG\Property(
     *                 property="director",
     *                 type="object",
     *                 @SWG\Property(property="fullName", type="string"),
     *                 @SWG\Property(property="id", type="integer")
     *             ),
     *             @SWG\Property(property="id", type="string"),
     *             @SWG\Property(property="members", type="integer"),
     *             @SWG\Property(property="name", type="string"),
     *             @SWG\Property(
     *                 property="resume",
     *                 type="object",
     *                 @SWG\Property(property="actual", type="string", description="Actual date"),
     *                 @SWG\Property(property="prev", type="string", description="Expected date")
     *             ),
     *             @SWG\Property(property="suspDate", type="string", description="Suspension date"),
     *             @SWG\Property(property="warning", type="string", description="Available values: NULL, 'CORE_GROUP' or 'CHAPTER'")
     *         )
     *     )
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function getChapters(Region $region, Request $request): Response
    {
        $user = $this->getUser();
        $role = $request->get("role", null);
        $code = Response::HTTP_OK;

        if (!is_null($role) && !in_array($role, [
            $this->directorRepository::DIRECTOR_ROLE_AREA,
            $this->directorRepository::DIRECTOR_ROLE_ASSISTANT,
            $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE
        ])) {
            $code = Response::HTTP_BAD_REQUEST;
        } else {
            $checked = $this->directorRepository->checkDirectorRole($user, $region, $role);

            $code = Util::arrayGetValue($checked, 'errorCode', $code);
            if ($code == Response::HTTP_OK) {
                $director = Util::arrayGetValue($checked, 'director', null);
                if (is_null($director)) {
                    $code = Response::HTTP_BAD_REQUEST;
                }
            }
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
     *      name="role",
     *      in="query",
     *      type="string",
     *      description="Optional parameter to get data relative to the specified given role"
     * )
     * @SWG\Parameter(
     *
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns an array of Chapter objects",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *             type="object",
     *             @SWG\Property(
     *                 property="chapterLaunch",
     *                 type="object",
     *                 @SWG\Property(property="actual", type="string", description="Actual date"),
     *                 @SWG\Property(property="prev", type="string", description="Expected date")
     *             ),
     *             @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *             @SWG\Property(
     *                 property="coreGroupLaunch",
     *                 type="object",
     *                 @SWG\Property(property="actual", type="string", description="Actual date"),
     *                 @SWG\Property(property="prev", type="string", description="Expected date")
     *             ),
     *             @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *             @SWG\Property(
     *                 property="director",
     *                 type="object",
     *                 @SWG\Property(property="fullName", type="string"),
     *                 @SWG\Property(property="id", type="integer")
     *             ),
     *             @SWG\Property(property="id", type="string"),
     *             @SWG\Property(property="members", type="integer"),
     *             @SWG\Property(property="name", type="string"),
     *             @SWG\Property(
     *                 property="resume",
     *                 type="object",
     *                 @SWG\Property(property="actual", type="string", description="Actual date"),
     *                 @SWG\Property(property="prev", type="string", description="Expected date")
     *             )
     *             @SWG\Property(property="suspDate", type="string", description="Suspension date"),
     *             @SWG\Property(property="warning", type="string", description="Available values: NULL, 'CORE_GROUP' or 'CHAPTER'")
     *         )
     *     )
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="Bearer")
     *
     * @return Response
     */
}
