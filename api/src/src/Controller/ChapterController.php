<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Region;
use App\Formatter\ChapterFormatter;
use App\Repository\ChapterRepository;
use DateTime;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChapterController extends AbstractController
{
    /** @var ChapterFormatter */
    private $chapterFormatter;

    /** @var ChapterRepository */
    private $chapterRepository;

    /**
     * @param ChapterFormatter $chapterFormatter
     * @param ChapterRepository $chapterRepository
     */
    public function __construct(
        ChapterFormatter $chapterFormatter,
        ChapterRepository $chapterRepository
    ) {
        $this->chapterFormatter = $chapterFormatter;
        $this->chapterRepository = $chapterRepository;
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
     *                 @SWG\Property(property="prev", type="string", description="Expected date"),
     *                 @SWG\Property(property="actual", type="string", description="Actual date")
     *             ),
     *             @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *             @SWG\Property(
     *                 property="coreGroupLaunch",
     *                 type="object",
     *                 @SWG\Property(property="prev", type="string", description="Expected date"),
     *                 @SWG\Property(property="actual", type="string", description="Actual date")
     *             ),
     *             @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *             @SWG\Property(
     *                 property="director",
     *                 type="object",
     *                 @SWG\Property(property="id", type="integer"),
     *                 @SWG\Property(property="fullName", type="string")
     *             ),
     *             @SWG\Property(property="id", type="string"),
     *             @SWG\Property(property="members", type="integer"),
     *             @SWG\Property(property="name", type="string"),
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
    public function getChapters(Region $region): Response
    {
        $chapters = $this->chapterRepository->findAll();

        return new JsonResponse(array_map(function ($c) {
            /** @var Chapter $c */
            $ret = $this->chapterFormatter->formatBase($c);

            $today = new DateTime();
            $warning = null;

            if (is_null($c->getActualLaunchCoregroupDate()) && $c->getPrevLaunchCoregroupDate() <= $today) {
                // Check for core group launch date
                $warning = "CORE_GROUP";
            } elseif (is_null($c->getActualLaunchChatperDate()) && $c->getPrevLaunchChatperDate() <= $today) {
                // Check for chapter launch date
                $warning = "CHAPTER";
            }

            $ret['warning'] = $warning;

            return $ret;
        }, $chapters));
    }
}
