<?php

namespace App\Controller;

use App\Formatter\ChapterFormatter;
use App\Repository\ChapterRepository;
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
     * @Route(path="/chapters", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a User object representing me",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(
     *             property="chapterLaunch",
     *             type="object",
     *             @SWG\Property(property="prev", type="string", description="Expected date"),
     *             @SWG\Property(property="actual", type="string", description="Actual date")
     *         ),
     *         @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *         @SWG\Property(
     *             property="coreGroupLaunch",
     *             type="object",
     *             @SWG\Property(property="prev", type="string", description="Expected date"),
     *             @SWG\Property(property="actual", type="string", description="Actual date")
     *         ),
     *         @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *         @SWG\Property(
     *             property="director",
     *             type="object",
     *             @SWG\Property(property="id", type="integer"),
     *             @SWG\Property(property="fullName", type="string")
     *         ),
     *         @SWG\Property(property="id", type="integer"),
     *         @SWG\Property(property="members", type="integer"),
     *         @SWG\Property(property="name", type="string"),
     *         @SWG\Property(property="suspDate", type="string", description="Suspension date"),
     *         @SWG\Property(property="warning", type="string", description="Available values: NULL, 'CORE_GROUP' or 'CHAPTER'")
     *     )
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="none")
     *
     * @return Response
     */
    public function getChapters(): Response
    {
        $chapters = $this->chapterRepository->findAll();

        return new JsonResponse(array_map(function ($c) {
            $ret = $this->chapterFormatter->formatFull($c, $this->chapterFormatter::DIRECTOR_BASE_DATA);

            return $ret;
        }, $chapters));
    }
}
