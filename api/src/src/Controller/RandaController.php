<?php

namespace App\Controller;

use App\Entity\Randa;
use App\Entity\Region;
use App\Formatter\RandaFormatter;
use App\Repository\DirectorRepository;
use App\Repository\NewMemberRepository;
use App\Repository\RandaRepository;
use App\Repository\RenewedMemberRepository;
use App\Repository\RetentionRepository;
use App\Repository\UserRepository;
use App\Util\Constants;
use App\Util\Util;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

    /** @var RenewedMemberRepository */
    private $renewedMemberRepository;

    /** @var RetentionRepository */
    private $retentionRepository;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(
        DirectorRepository $directorRepository,
        NewMemberRepository $newMemberRepository,
        RandaFormatter $randaFormatter,
        RandaRepository $randaRepository,
        RenewedMemberRepository $renewedMemberRepository,
        RetentionRepository $retentionRepository,
        UserRepository $userRepository
    ) {
        $this->directorRepository = $directorRepository;
        $this->newMemberRepository = $newMemberRepository;
        $this->randaFormatter = $randaFormatter;
        $this->randaRepository = $randaRepository;
        $this->renewedMemberRepository = $renewedMemberRepository;
        $this->retentionRepository = $retentionRepository;
        $this->userRepository = $userRepository;
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
        ], [
            'currentTimeslot' => 'DESC'
        ]);

        $ranas = $randa->getRanas();

        foreach ($ranas as &$rana) {
            $params = [
                'rana' => $rana,
                'timeslot' => $randa->getCurrentTimeslot(),
                'valueType' => Constants::VALUE_TYPE_PROPOSED
            ];
            $rana->filteredNewMembers = $this->newMemberRepository->findOneBy($params);
            $rana->filteredRenewedMembers = $this->renewedMemberRepository->findOneBy($params);
            $rana->filteredRetentionMembers = $this->retentionRepository->findOneBy($params);
        }

        $randa->filteredRanas = $ranas;

        return new JsonResponse($this->randaFormatter->formatData($randa));
    }
}
