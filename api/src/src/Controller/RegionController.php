<?php

namespace App\Controller;

use App\Entity\Region;
use App\Formatter\RegionFormatter;
use App\OldDB\Entity\Region as OldRegion;
use App\OldDB\Repository\RegionRepository as OldRegionRepository;
use App\Repository\DirectorRepository;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
        $directors = $regions = [];
        $regions_roles = [];

        if ($user->isAdmin()) {
            $regions = $this->regionRepository->findAll();
        } else {
            foreach ($this->directorRepository->findByUser($user) as $director) {
                $directorId = $director->getId();
                if (!array_key_exists($directorId, $directors)) {
                    $directors[$directorId] = $director;
                }
                foreach ($this->directorRepository->findBySupervisor($director) as $subordinate) {
                    $subordinateId = $subordinate->getId();
                    if (!array_key_exists($subordinateId, $directors)) {
                        $directors[$subordinateId] = $subordinate;
                    }
                }
            }

            foreach ($directors as $director) {
                $region = $director->getRegion();
                $regionId = $region->getId();
                if (!array_key_exists($regionId, $regions)) {
                    $regions[$regionId] = $region;
                    $regions_roles[$regionId] = $director->getRole();
                }
            }
            $regions = array_values($regions);
        }
        usort($regions, function ($r1, $r2) {
            return $r1->getName() < $r2->getName() ? -1 : ($r1->getName() > $r2->getName() ? 1 : 0);
        });

        return new JsonResponse(array_map(function ($region) use($regions_roles, $user) {
            $role = $user->isAdmin() ? "ADMIN" : $regions_roles[$region->getId()];
            return $this->regionFormatter->formatWithRole($region, $role);
        }, $regions));
    }

    /**
     * Importer for regions from the old DB
     *
     * @Route(path="/regions/import", name="region_importer", methods={"GET","PATCH"})
     *
     * @SWG\Response(
     *      response=200,
     *      description="All regions has been imported correctly"
     * )
     * @SWG\Response(
     *      response=500,
     *      description="Some import error occured",
     *      @SWG\Schema(type="string", description="The error message")
     * )
     * @SWG\Tag(name="Regions")
     * @Security(name="none")
     *
     * @return Response
     */
    public function importRegionsFromOldDB(): Response
    {
        //TODO: Rimuovere o commentare la riga qui sotto se si intende rieseguire l'importazione
        return new JsonResponse();

        //Entity managers and repositories
        /** @var EntityManagerInterface */
        $em = $this->getDoctrine()->getManager('default');

        /** @var RegionRepository */
        $regionRepository = $this->getDoctrine()->getRepository(Region::class, 'default');

        /** @var OldRegionRepository */
        $oldRegionRepository = $this->getDoctrine()->getRepository(OldRegion::class, 'old_db');

        //Retrieve data from old table
        $oldRegions = $oldRegionRepository->findAll();

        //Truncate the Region table
        $classMetaData = $em->getClassMetadata(Region::class);
        $connection = $em->getConnection();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->query('DELETE FROM ' . $classMetaData->getTableName());
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (Exception $ex) {
            $connection->rollBack();
            die("Oooops!");
        }

        //Fill the new Region table
        $connection->beginTransaction();
        try {
            foreach ($oldRegions as $oldRegion) {
                $region = new Region();
                $region->setName($oldRegion->getNome());
                $region->setNotes($oldRegion->getNotaP());
                $em->persist($region);
                $em->flush();
            }
            $connection->commit();
        } catch (Exception $ex) {
            $connection->rollBack();
            return new JsonResponse($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse();
    }
}
