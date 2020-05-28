<?php

namespace App\Controller;

use App\Formatter\RegionFormatter;
use App\Repository\DirectorRepository;
use App\Repository\RegionRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegionController extends AbstractController
{
    /** @var DirectorRepository */
    private $directorRepository;

    /** @var RegionFormatter */
    private $regionFormatter;

    /** @var RegionRepository */
    private $regionRepository;

    public function __construct(
        DirectorRepository $directorRepository,
        RegionFormatter $regionFormatter,
        RegionRepository $regionRepository
    ) {
        $this->directorRepository = $directorRepository;
        $this->regionFormatter = $regionFormatter;
        $this->regionRepository = $regionRepository;
    }

    /**
     * Get regions
     *
     * @Route(path="/regions", name="user_regions_list", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns an array of Region objects",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *             type="object",
     *             @SWG\Property(property="id", type="string"),
     *             @SWG\Property(property="name", type="string"),
     *             @SWG\Property(property="notes", type="string")
     *         )
     *     )
     * )
     * @SWG\Tag(name="Regions")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function getRegions(): Response
    {
        $user = $this->getUser();
        $directors = [];
        $regions = [];

        foreach ($this->directorRepository->findByUser($user) as $director) {
            $directorId = $director->getId()->toString();
            if (!array_Key_exists($directorId, $directors)) {
                $directors[$directorId] = $director;
            }
            foreach ($this->directorRepository->findBySupervisor($director) as $subordinate) {
                $subordinateId = $subordinate->getId()->toString();
                if (!array_key_exists($subordinateId, $directors)) {
                    $directors[$subordinateId] = $subordinate;
                }
            }
        }

        foreach ($directors as $director) {
            $region = $director->getRegion();
            $regionId = $region->getId()->toString();
            if (!array_key_exists($regionId, $regions)) {
                $regions[$regionId] = $region;
            }
        }
        $regions = array_values($regions);

        return new JsonResponse(array_map(function ($region) {
            return $this->regionFormatter->formatBase($region);
        }, $regions));
    }
}
