<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\NewMember;
use App\Entity\Rana;
use App\Entity\RanaLifecycle;
use App\Entity\Randa;
use App\Entity\Retention;
use App\Formatter\NewMemberFormatter;
use App\Formatter\RanaFormatter;
use App\Repository\DirectorRepository;
use App\Repository\NewMemberRepository;
use App\Repository\RanaLifecycleRepository;
use App\Repository\RanaRepository;
use App\Repository\RandaRepository;
use App\Repository\RenewedMemberRepository;
use App\Repository\RetentionRepository;
use App\Repository\UserRepository;
use App\Util\Constants;
use App\Util\Util;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/api")
**/
class RanaController extends AbstractController
{
    /** @var DirectorRepository */
    private $directorRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var RanaFormatter */
    private $ranaFormatter;

    /** @var RanaLifecycleRepository */
    private $ranaLifeCycleRepository;

    /** @var RanaRepository */
    private $ranaRepository;

    /** @var RandaRepository */
    private $randaRepository;

    /** @var NewMemberRepository */
    private $newMemberRepository;

    /** @var RetentionRepository */
    private $retentionRepository;

    /** @var UserRepository */
    private $userRepository;

    /** @var NewMemberFormatter */
    private $newMemberFormatter;

    public function __construct(
        DirectorRepository $directorRepository,
        EntityManagerInterface $entityManager,
        NewMemberRepository $newMemberRepository,
        RanaFormatter $ranaFormatter,
        RanaLifecycleRepository $ranaLifeCycleRepository,
        RanaRepository $ranaRepository,
        RandaRepository $randaRepository,
        RenewedMemberRepository $renewedMemberRepository,
        RetentionRepository $retentionRepository,
        UserRepository $userRepository,
        NewMemberFormatter $newMemberFormatter

    ) {
        $this->directorRepository = $directorRepository;
        $this->entityManager = $entityManager;
        $this->newMemberFormatter = $newMemberFormatter;
        $this->newMemberRepository = $newMemberRepository;
        $this->ranaFormatter = $ranaFormatter;
        $this->ranaLifeCycleRepository = $ranaLifeCycleRepository;
        $this->ranaRepository = $ranaRepository;
        $this->randaRepository = $randaRepository;
        $this->renewedMemberRepository = $renewedMemberRepository;
        $this->retentionRepository = $retentionRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Create a Rana
     *
     * @Route(path="/{id}/rana", name="create_rana", methods={"POST"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The chapter"
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
     *      description="Returns a Rana object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(
     *              property="newMembers",
     *              type="object",
     *              @SWG\Property(
     *                  property="APPR",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="CONS",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="PROP",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              )
     *          ),
     *          @SWG\Property(
     *              property="renewedMembers",
     *              type="object",
     *              @SWG\Property(
     *                  property="APPR",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="CONS",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="PROP",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              )
     *          ),
     *          @SWG\Property(
     *              property="retention",
     *              type="object",
     *              @SWG\Property(
     *                  property="APPR",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="CONS",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="PROP",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              )
     *          )
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if role is given but is not valid or if rana can not be created."
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin, if a valid role is given but the user has not that role for the specified region or the role is not enought for the operation."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Rana")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function createRana(Chapter $chapter, Request $request): Response
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
            $currentYear = (int) date("Y");
            $randa = $this->randaRepository->findOneBy([
                'region' => $region,
                'year' => $currentYear
            ]);

            if (is_null($randa)) {
                $randa = new Randa();
                $randa->setCurrentTimeslot(Constants::TIMESLOT_T0);
                $randa->setRegion($region);
                $randa->setYear($currentYear);
                $randa->setCurrentState("TODO");
                $this->randaRepository->save($randa);
            }

            $rana = $this->ranaRepository->findOneBy([
                'chapter' => $chapter,
                'randa' => $randa
            ]);

            if (!is_null($rana)) {
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

        if ($code == Response::HTTP_OK) {
            $rana = new Rana();
            $rana->setChapter($chapter);
            $rana->setRanda($randa);
            $this->ranaRepository->save($rana);

            $ranaLifeCycle = new RanaLifecycle();
            $ranaLifeCycle->setCurrentState(Constants::RANA_LIFECYCLE_STATUS_TODO);
            $ranaLifeCycle->setCurrentTimeslot(Constants::TIMESLOT_T0);
            $ranaLifeCycle->setRana($rana);
            $this->ranaLifeCycleRepository->save($ranaLifeCycle);
            $this->entityManager->refresh($rana);

            return new JsonResponse($this->ranaFormatter->formatData($rana, $role, "", $randa->getCurrentTimeslot(), null));
        }
    }

    /**
     * Create or update a members block
     *
     * @Route(path="/{id}/rana-members", name="manage_rana_members", methods={"POST"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The rana"
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
     * @SWG\Parameter(
     *      name="timeslot",
     *      in="formData",
     *      type="string",
     *      description="One between T0 and T4",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="valueType",
     *      in="formData",
     *      type="string",
     *      description="One between PROP, APPR and CONS",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="n_m1",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="n_m2",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="n_m3",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="n_m4",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="n_m5",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="n_m6",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="n_m7",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="n_m8",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="n_m9",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="n_m10",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="n_m11",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="n_m12",
     *      in="formData",
     *      type="integer"
     * )
     * * @SWG\Parameter(
     *      name="r_m1",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="r_m2",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="r_m3",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="r_m4",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="r_m5",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="r_m6",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="r_m7",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="r_m8",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="r_m9",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="r_m10",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="r_m11",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="r_m12",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns a Rana object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(
     *              property="newMembers",
     *              type="object",
     *              @SWG\Property(
     *                  property="APPR",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="CONS",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="PROP",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              )
     *          ),
     *          @SWG\Property(
     *              property="renewedMembers",
     *              type="object",
     *              @SWG\Property(
     *                  property="APPR",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="CONS",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="PROP",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              )
     *          ),
     *          @SWG\Property(
     *              property="retention",
     *              type="object",
     *              @SWG\Property(
     *                  property="APPR",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="CONS",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="PROP",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              )
     *          )
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if role is given but is not valid or if rana can not be created."
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin, if a valid role is given but the user has not that role for the specified region or the role is not enought for the operation."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Rana")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function createMembers(Rana $rana, Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $chapter = $rana->getChapter();
        $region = $chapter->getRegion();

        $roleCheck = [
            Constants::ROLE_EXECUTIVE,
            Constants::ROLE_AREA,
            Constants::ROLE_ASSISTANT
        ];
        $performerData = Util::getPerformerData($this->getUser(), $region, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), $request->get("role"));
        header("log79: errors");

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

        if ($code == Response::HTTP_OK && !$isAdmin) {
            if ($role == Constants::ROLE_ASSISTANT && $chapter->getDirector() != $director) {
                $code = Response::HTTP_FORBIDDEN;
            }

            if ($role == Constants::ROLE_AREA && $chapter->getDirector() != $director) {                     
                $code = Response::HTTP_FORBIDDEN;
            }
        } else {
            header("log7: errors");
        }

        if ($code == Response::HTTP_OK) {
            $valueType = $request->get("valueType");

            $errorFields = [];

            $availableTimeslots = [
                Constants::TIMESLOT_T0,
                Constants::TIMESLOT_T1,
                Constants::TIMESLOT_T2,
                Constants::TIMESLOT_T3,
                Constants::TIMESLOT_T4
            ];
            $availableValueTypes = [
                Constants::VALUE_TYPE_APPR,
                Constants::VALUE_TYPE_CONSUMPTIVE,
                Constants::VALUE_TYPE_PROP
            ];

            if (!in_array($timeslot, $availableTimeslots)) {
                $errorFields['timeslot'] = "invalid";
            } else {
                $slotNumber = (int) substr($timeslot, -1);
                $nextSlotNumber = $slotNumber + 1;
                $nextTimeslot = "T$nextSlotNumber";

                $nextNewMembers = $this->newMemberRepository->findBy([
                    'rana' => $rana,
                    'timeslot' => $nextTimeslot,
                    'valueType' => [
                        Constants::VALUE_TYPE_APPR,
                        Constants::VALUE_TYPE_CONSUMPTIVE
                    ]
                ]);

                $nextRetentions = $this->retentionRepository->findBy([
                    'rana' => $rana,
                    'timeslot' => $nextTimeslot,
                    'valueType' => [
                        Constants::VALUE_TYPE_APPR,
                        Constants::VALUE_TYPE_CONSUMPTIVE
                    ]
                ]);

                if (!empty($nextRetentions) || !empty($nextNewMembers)) {
                    $errorFields['timeslot'] = "invalid";
                }

                if ($slotNumber && $slotNumber > 1) {
                    $prevSlotNumber = $slotNumber - 1;
                    $prevTimeslot = "T$prevSlotNumber";

                    $prevNewMembers = $this->newMemberRepository->findBy([
                        'rana' => $rana,
                        'timeslot' => $prevTimeslot
                    ]);

                    $prevRetentions = $this->retentionRepository->findBy([
                        'rana' => $rana,
                        'timeslot' => $prevTimeslot
                    ]);

                    if (empty($prevNewMembers) || empty($prevRetentions)) {
                        $errorFields['timeslot'] = "invalid";
                    }
                }
            }

            if (empty($errorFields)) {
                if (!in_array($valueType, $availableValueTypes)) {
                    $errorFields['valueType'] = "invalid";
                } else {
                    switch ($valueType) {
                        case Constants::VALUE_TYPE_CONSUMPTIVE:
                            if ($timeslot == Constants::TIMESLOT_T0) {
                                $errorFields['valueType'] = "invalid";
                            }
                            break;
                        case Constants::VALUE_TYPE_APPR:
                            if ($role != Constants::ROLE_EXECUTIVE) {
                                $code = Response::HTTP_FORBIDDEN;
                            } elseif ($timeslot == Constants::TIMESLOT_T4) {
                                $errorFields['valueType'] = "invalid";
                            } else {
                                $lastRetentions = $this->retentionRepository->findOneBy([
                                    'rana' => $rana,
                                    'timeslot' => $timeslot,
                                    'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE
                                ]);

                                $lastNewMembers = $this->newMemberRepository->findOneBy([
                                    'rana' => $rana,
                                    'timeslot' => $timeslot,
                                    'valueType' => Constants::VALUE_TYPE_CONSUMPTIVE
                                ]);

                                if (!is_null($lastRetentions) || !is_null($lastNewMembers)) {
                                    $errorFields['valueType'] = "invalid";
                                }
                            }
                            break;
                        case Constants::VALUE_TYPE_PROP:
                            if ($timeslot == Constants::TIMESLOT_T4) {
                                $errorFields['valueType'] = "invalid";
                            } else {
                                $lastRetentions = $this->retentionRepository->findBy([
                                    'rana' => $rana,
                                    'timeslot' => $timeslot,
                                    'valueType' => [
                                        Constants::VALUE_TYPE_CONSUMPTIVE,
                                        Constants::VALUE_TYPE_APPR
                                    ]
                                ]);

                                $lastNewMembers = $this->newMemberRepository->findBy([
                                    'rana' => $rana,
                                    'timeslot' => $timeslot,
                                    'valueType' => [
                                        Constants::VALUE_TYPE_CONSUMPTIVE,
                                        Constants::VALUE_TYPE_APPR
                                    ]
                                ]);

                                if (!empty($lastNewMembers) || !empty($lastRetentions)) {
                                    $errorFields['valueType'] = "invalid";
                                }
                            }
                            break;
                    }
                }
            }


            if (!empty($errorFields)) {
                $code = Response::HTTP_BAD_REQUEST;
                return new JsonResponse($errorFields, $code);
            }
        } else {
            header("log5: errors");
        }

        if ($code == Response::HTTP_OK) {
            $isNewNewMember = false;
            $isNewRetentionMember = false;

            $newMember = $this->newMemberRepository->findOneBy([
                'rana' => $rana,
                'timeslot' => $timeslot,
                'valueType' => $valueType
            ]);

            if (is_null($newMember)) {
                $newMember = new NewMember();
                $isNewNewMember = true;
            }

            $retentionMember = $this->retentionRepository->findOneBy([
                'rana' => $rana,
                'timeslot' => $timeslot,
                'valueType' => $valueType
            ]);

            if (is_null($retentionMember)) {
                $retentionMember = new Retention();
                $isNewRetentionMember = true;
            }

            $newMember->setRana($rana);
            $newMember->setTimeslot($timeslot);

            $newMember->setValueType($valueType);

            $retentionMember->setRana($rana);
            $retentionMember->setTimeslot($timeslot);
            $retentionMember->setValueType($valueType);

            if ($valueType == Constants::VALUE_TYPE_CONSUMPTIVE) {
                switch ($timeslot) {
                    case Constants::TIMESLOT_T4:
                        $previous = $this->newMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => Constants::TIMESLOT_T3
                        ]);
                        $newMember->setM12($request->get("n_m12") ?? ($previous ? ($previous->getM12() ? $previous->getM12() : 0) : 0));
                        $newMember->setM11($request->get("n_m11") ?? ($previous ? ($previous->getM11() ? $previous->getM11() : 0) : 0));
                        $newMember->setM10($request->get("n_m10") ?? ($previous ? ($previous->getM10() ? $previous->getM10() : 0) : 0));

                        $previous = $this->retentionRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => Constants::TIMESLOT_T3
                        ]);
                        $retentionMember->setM12($request->get("r_m12") ?? ($previous ? ($previous->getM12() ? $previous->getM12() : 0) : 0));
                        $retentionMember->setM11($request->get("r_m11") ?? ($previous ? ($previous->getM11() ? $previous->getM11() : 0) : 0));
                        $retentionMember->setM10($request->get("r_m10") ?? ($previous ? ($previous->getM10() ? $previous->getM10() : 0) : 0));
                    case Constants::TIMESLOT_T3:
                        $previous = $this->newMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => Constants::TIMESLOT_T2
                        ]);
                        $newMember->setM9($request->get("n_m9") ?? ($previous ? ($previous->getM9() ? $previous->getM9() : 0) : 0));
                        $newMember->setM8($request->get("n_m8") ?? ($previous ? ($previous->getM8() ? $previous->getM8() : 0) : 0));
                        $newMember->setM7($request->get("n_m7") ?? ($previous ? ($previous->getM7() ? $previous->getM7() : 0) : 0));

                        $previous = $this->retentionRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => Constants::TIMESLOT_T2
                        ]);
                        $retentionMember->setM9($request->get("r_m9") ?? ($previous ? ($previous->getM9() ? $previous->getM9() : 0) : 0));
                        $retentionMember->setM8($request->get("r_m8") ?? ($previous ? ($previous->getM8() ? $previous->getM8() : 0) : 0));
                        $retentionMember->setM7($request->get("r_m7") ?? ($previous ? ($previous->getM7() ? $previous->getM7() : 0) : 0));
                    case Constants::TIMESLOT_T2:
                        $previous = $this->newMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => Constants::TIMESLOT_T1
                        ]);
                        $newMember->setM6($request->get("n_m6") ?? ($previous ? ($previous->getM6() ? $previous->getM6() : 0) : 0));
                        $newMember->setM5($request->get("n_m5") ?? ($previous ? ($previous->getM5() ? $previous->getM6() : 0) : 0));
                        $newMember->setM4($request->get("n_m4") ?? ($previous ? ($previous->getM4() ? $previous->getM4() : 0) : 0));

                        $previous = $this->retentionRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => Constants::TIMESLOT_T1
                        ]);
                        $retentionMember->setM6($request->get("r_m6") ?? ($previous ? ($previous->getM6() ? $previous->getM6() : 0) : 0));
                        $retentionMember->setM5($request->get("r_m5") ?? ($previous ? ($previous->getM5() ? $previous->getM6() : 0) : 0));
                        $retentionMember->setM4($request->get("r_m4") ?? ($previous ? ($previous->getM4() ? $previous->getM4() : 0) : 0));
                    case Constants::TIMESLOT_T1:
                        $newMember->setM3($request->get("n_m3") ?? 0);
                        $newMember->setM2($request->get("n_m2") ?? 0);
                        $newMember->setM1($request->get("n_m1") ?? 0);

                        $retentionMember->setM3($request->get("r_m3") ?? 0);
                        $retentionMember->setM2($request->get("r_m2") ?? 0);
                        $retentionMember->setM1($request->get("r_m1") ?? 0);
                }
            } else {

                switch ($timeslot) {
                    case Constants::TIMESLOT_T0:
                        $newMember->setM1($request->get("n_m1") ?? 0);
                        $newMember->setM2($request->get("n_m2") ?? 0);
                        $newMember->setM3($request->get("n_m3") ?? 0);

                        $retentionMember->setM1($request->get("r_m1") ?? 0);
                        $retentionMember->setM2($request->get("r_m2") ?? 0);
                        $retentionMember->setM3($request->get("r_m3") ?? 0);
                    case Constants::TIMESLOT_T1:
                        $previousNewMember = $this->newMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => Constants::TIMESLOT_T0
                        ]);

                        $previousRetentionMember = $this->retentionRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => Constants::TIMESLOT_T0
                        ]);

                        if ($timeslot == Constants::TIMESLOT_T1) {
                            $newMember->setM1($request->get("n_m1") ?? ($previousNewMember ? ($previousNewMember->getM1() ? $previousNewMember->getM1() : 0) : 0));
                            $newMember->setM2($request->get("n_m2") ?? ($previousNewMember ? ($previousNewMember->getM2() ? $previousNewMember->getM2() : 0) : 0));
                            $newMember->setM3($request->get("n_m3") ?? ($previousNewMember ? ($previousNewMember->getM3() ? $previousNewMember->getM3() : 0) : 0));
                            $retentionMember->setM1($request->get("r_m1") ?? ($previousRetentionMember ? ($previousRetentionMember->getM1() ? $previousRetentionMember->getM1() : 0) : 0));
                            $retentionMember->setM2($request->get("r_m2") ?? ($previousRetentionMember ? ($previousRetentionMember->getM2() ? $previousRetentionMember->getM2() : 0) : 0));
                            $retentionMember->setM3($request->get("r_m3") ?? ($previousRetentionMember ? ($previousRetentionMember->getM3() ? $previousRetentionMember->getM3() : 0) : 0));
                        }

                        $newMember->setM4($request->get("n_m4") ?? ($previousNewMember ? ($previousNewMember->getM4() ? $previousNewMember->getM4() : 0) : 0));
                        $newMember->setM5($request->get("n_m5") ?? ($previousNewMember ? ($previousNewMember->getM5() ? $previousNewMember->getM5() : 0) : 0));
                        $newMember->setM6($request->get("n_m6") ?? ($previousNewMember ? ($previousNewMember->getM6() ? $previousNewMember->getM6() : 0) : 0));

                        $retentionMember->setM4($request->get("r_m4") ?? ($previousRetentionMember ? ($previousRetentionMember->getM4() ? $previousRetentionMember->getM4() : 0) : 0));
                        $retentionMember->setM5($request->get("r_m5") ?? ($previousRetentionMember ? ($previousRetentionMember->getM5() ? $previousRetentionMember->getM5() : 0) : 0));
                        $retentionMember->setM6($request->get("r_m6") ?? ($previousRetentionMember ? ($previousRetentionMember->getM6() ? $previousRetentionMember->getM6() : 0) : 0));

                    case Constants::TIMESLOT_T2:
                        $previousNewMember = $this->newMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => Constants::TIMESLOT_T1
                        ]);
                        $previousRetentionMember = $this->retentionRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => Constants::TIMESLOT_T1
                        ]);
                        if ($timeslot == Constants::TIMESLOT_T2) {
                            $newMember->setM1($request->get("n_m1") ?? ($previousNewMember ? ($previousNewMember->getM1() ? $previousNewMember->getM1() : 0) : 0));
                            $newMember->setM2($request->get("n_m2") ?? ($previousNewMember ? ($previousNewMember->getM2() ? $previousNewMember->getM2() : 0) : 0));
                            $newMember->setM3($request->get("n_m3") ?? ($previousNewMember ? ($previousNewMember->getM3() ? $previousNewMember->getM3() : 0) : 0));
                            $newMember->setM4($request->get("n_m4") ?? ($previousNewMember ? ($previousNewMember->getM4() ? $previousNewMember->getM4() : 0) : 0));
                            $newMember->setM5($request->get("n_m5") ?? ($previousNewMember ? ($previousNewMember->getM5() ? $previousNewMember->getM5() : 0) : 0));
                            $newMember->setM6($request->get("n_m6") ?? ($previousNewMember ? ($previousNewMember->getM6() ? $previousNewMember->getM6() : 0) : 0));

                            $retentionMember->setM1($request->get("r_m1") ?? ($previousRetentionMember ? ($previousRetentionMember->getM1() ? $previousRetentionMember->getM1() : 0) : 0));
                            $retentionMember->setM2($request->get("r_m2") ?? ($previousRetentionMember ? ($previousRetentionMember->getM2() ? $previousRetentionMember->getM2() : 0) : 0));
                            $retentionMember->setM3($request->get("r_m3") ?? ($previousRetentionMember ? ($previousRetentionMember->getM3() ? $previousRetentionMember->getM3() : 0) : 0));
                            $retentionMember->setM4($request->get("r_m4") ?? ($previousRetentionMember ? ($previousRetentionMember->getM4() ? $previousRetentionMember->getM4() : 0) : 0));
                            $retentionMember->setM5($request->get("r_m5") ?? ($previousRetentionMember ? ($previousRetentionMember->getM5() ? $previousRetentionMember->getM5() : 0) : 0));
                            $retentionMember->setM6($request->get("r_m6") ?? ($previousRetentionMember ? ($previousRetentionMember->getM6() ? $previousRetentionMember->getM6() : 0) : 0));
                        }

                        $newMember->setM7($request->get("n_m7") ?? ($previousNewMember ? ($previousNewMember->getM7() ? $previousNewMember->getM7() : 0) : 0));
                        $newMember->setM8($request->get("n_m8") ?? ($previousNewMember ? ($previousNewMember->getM8() ? $previousNewMember->getM8() : 0) : 0));
                        $newMember->setM9($request->get("n_m9") ?? ($previousNewMember ? ($previousNewMember->getM9() ? $previousNewMember->getM9() : 0) : 0));
                        $retentionMember->setM7($request->get("r_m7") ?? ($previousRetentionMember ? ($previousRetentionMember->getM7() ? $previousRetentionMember->getM7() : 0) : 0));
                        $retentionMember->setM8($request->get("r_m8") ?? ($previousRetentionMember ? ($previousRetentionMember->getM8() ? $previousRetentionMember->getM8() : 0) : 0));
                        $retentionMember->setM9($request->get("r_m9") ?? ($previousRetentionMember ? ($previousRetentionMember->getM9() ? $previousRetentionMember->getM9() : 0) : 0));
                    case Constants::TIMESLOT_T3:
                        $previousNewMember = $this->newMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => Constants::TIMESLOT_T2
                        ]);
                        $previousRetentionMember = $this->retentionRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => Constants::TIMESLOT_T2
                        ]);

                        if ($timeslot == Constants::TIMESLOT_T3) {
                            $newMember->setM1($request->get("n_m1") ?? ($previousNewMember ? ($previousNewMember->getM1() ? $previousNewMember->getM1() : 0) : 0));
                            $newMember->setM2($request->get("n_m2") ?? ($previousNewMember ? ($previousNewMember->getM2() ? $previousNewMember->getM2() : 0) : 0));
                            $newMember->setM3($request->get("n_m3") ?? ($previousNewMember ? ($previousNewMember->getM3() ? $previousNewMember->getM3() : 0) : 0));
                            $newMember->setM4($request->get("n_m4") ?? ($previousNewMember ? ($previousNewMember->getM4() ? $previousNewMember->getM4() : 0) : 0));
                            $newMember->setM5($request->get("n_m5") ?? ($previousNewMember ? ($previousNewMember->getM5() ? $previousNewMember->getM5() : 0) : 0));
                            $newMember->setM6($request->get("n_m6") ?? ($previousNewMember ? ($previousNewMember->getM6() ? $previousNewMember->getM6() : 0) : 0));
                            $newMember->setM7($request->get("n_m7") ?? ($previousNewMember ? ($previousNewMember->getM7() ? $previousNewMember->getM7() : 0) : 0));
                            $newMember->setM8($request->get("n_m8") ?? ($previousNewMember ? ($previousNewMember->getM8() ? $previousNewMember->getM8() : 0) : 0));
                            $newMember->setM9($request->get("n_m9") ?? ($previousNewMember ? ($previousNewMember->getM9() ? $previousNewMember->getM9() : 0) : 0));
                            
                            
                            $retentionMember->setM1($request->get("r_m1") !== null ? $request->get("r_m1") : ($previousRetentionMember ? ($previousRetentionMember->getM1() ? $previousRetentionMember->getM1() : 0) : 0));
                            $retentionMember->setM2($request->get("r_m2") !== null ? $request->get("r_m2") : ($previousRetentionMember ? ($previousRetentionMember->getM2() ? $previousRetentionMember->getM2() : 0) : 0));
                            $retentionMember->setM3($request->get("r_m3") !== null ? $request->get("r_m3") : ($previousRetentionMember ? ($previousRetentionMember->getM3() ? $previousRetentionMember->getM3() : 0) : 0));
                            $retentionMember->setM4($request->get("r_m4") !== null ? $request->get("r_m4") : ($previousRetentionMember ? ($previousRetentionMember->getM4() ? $previousRetentionMember->getM4() : 0) : 0));
                            $retentionMember->setM5($request->get("r_m5") !== null ? $request->get("r_m5") : ($previousRetentionMember ? ($previousRetentionMember->getM5() ? $previousRetentionMember->getM5() : 0) : 0));
                            $retentionMember->setM6($request->get("r_m6") !== null ? $request->get("r_m6") : ($previousRetentionMember ? ($previousRetentionMember->getM6() ? $previousRetentionMember->getM6() : 0) : 0));
                            $retentionMember->setM7($request->get("r_m7") !== null ? $request->get("r_m7") : ($previousRetentionMember ? ($previousRetentionMember->getM7() ? $previousRetentionMember->getM7() : 0) : 0));
                            $retentionMember->setM8($request->get("r_m8") !== null ? $request->get("r_m8") : ($previousRetentionMember ? ($previousRetentionMember->getM8() ? $previousRetentionMember->getM8() : 0) : 0));
                            $retentionMember->setM9($request->get("r_m9") !== null ? $request->get("r_m9") : ($previousRetentionMember ? ($previousRetentionMember->getM9() ? $previousRetentionMember->getM9() : 0) : 0));
                        }
                        $newMember->setM10($request->get("n_m10") !== null ? $request->get("n_m10") : ($previousNewMember ? ($previousNewMember->getM10() ? $previousNewMember->getM10() : 0) : 0));
                        $newMember->setM11($request->get("n_m11") !== null ? $request->get("n_m11") : ($previousNewMember ? ($previousNewMember->getM11() ? $previousNewMember->getM11() : 0) : 0));
                        $newMember->setM12($request->get("n_m12") !== null ? $request->get("n_m12") : ($previousNewMember ? ($previousNewMember->getM12() ? $previousNewMember->getM12() : 0) : 0));


                        $retentionMember->setM10($request->get("r_m10") !== null ? $request->get("r_m10") : ($previousRetentionMember ? ($previousRetentionMember->getM10() ? $previousRetentionMember->getM10() : 0) : 0));
                        $retentionMember->setM11($request->get("r_m11") !== null ? $request->get("r_m11") : ($previousRetentionMember ? ($previousRetentionMember->getM11() ? $previousRetentionMember->getM11() : 0) : 0));
                        $retentionMember->setM12($request->get("r_m12") !== null ? $request->get("r_m12") : ($previousRetentionMember ? ($previousRetentionMember->getM12() ? $previousRetentionMember->getM12() : 0) : 0));
                }
            }

            $this->newMemberRepository->save($newMember);
            $this->retentionRepository->save($retentionMember);

            file_put_contents("log", json_encode($this->newMemberFormatter->formatBase($newMember)));

            $state = Constants::RANA_LIFECYCLE_STATUS_TODO;
            switch ($valueType) {
                case Constants::VALUE_TYPE_APPR:

                    $state = Constants::RANA_LIFECYCLE_STATUS_APPR;

                    $proposedNewMembers = $this->newMemberRepository->findOneBy([
                        'rana' => $rana,
                        'timeslot' => $timeslot,
                        'valueType' => Constants::VALUE_TYPE_PROP
                    ]);
                    if (!$proposedNewMembers) {
                        $propNewMember = new NewMember();
                        $propNewMember->setRana($rana);
                        $propNewMember->setTimeslot($timeslot);
                        $propNewMember->setValueType(Constants::VALUE_TYPE_PROP);
                        for ($i = 1; $i <= 12; $i++) {
                            $method = "getM$i";
                            $propNewMember->setMonth($i, $newMember->$method());
                        }
                        $this->newMemberRepository->save($propNewMember);
                    } else {
                    }

                    $proposedRetention = $this->retentionRepository->findOneBy([
                        'rana' => $rana,
                        'timeslot' => $timeslot,
                        'valueType' => Constants::VALUE_TYPE_PROP
                    ]);
                    if (!$proposedRetention) {
                        $propRet = new Retention();
                        $propRet->setRana($rana);
                        $propRet->setTimeslot($timeslot);
                        $propRet->setValueType(Constants::VALUE_TYPE_PROP);
                        for ($i = 1; $i <= 12; $i++) {
                            $method = "getM$i";
                            $propRet->setMonth($i, $retentionMember->$method());
                        }
                        $this->retentionRepository->save($propRet);
                    }
                    break;
                case Constants::VALUE_TYPE_PROP:
                    $state = Constants::RANA_LIFECYCLE_STATUS_PROP;
                    break;
            }

            $currentLifeCycle = $this->ranaLifeCycleRepository->findOneBy([
                "rana" => $rana,
                "currentTimeslot" => $timeslot
            ]);

            //if lifecycle exists for this rana, update status and create a new lifecycle record with todo state for next t
            $currentLifeCycle->setCurrentState($state);
            $this->entityManager->persist($currentLifeCycle);
            $this->entityManager->flush();

            header("ok2: ok");

            $slotNumber = (int) substr($timeslot, -1);
            if ($slotNumber < 4) {
                $nextSlotNumber = $slotNumber + 1;
                $nextTimeslot = "T$nextSlotNumber";
            }

            $state = Constants::RANA_LIFECYCLE_STATUS_TODO;
        } else {
            $nextTimeslot = $timeslot;
            header("log6: errors");
        }

        // $nextRanaLifeCycle = $this->ranaLifeCycleRepository->findOneBy([
        //     "rana" => $rana,
        //     "currentTimeslot" => $nextTimeslot
        // ]);

        // if (!$nextRanaLifeCycle) {
        //     $nextRanaLifeCycle = new RanaLifecycle();
        //     $state = Constants::RANA_LIFECYCLE_STATUS_TODO;
        // }
        // $nextRanaLifeCycle->setCurrentState($state);
        // $nextRanaLifeCycle->setCurrentTimeslot($nextTimeslot);
        // $nextRanaLifeCycle->setRana($rana);

        // $this->ranaLifeCycleRepository->save($nextRanaLifeCycle);
        $this->entityManager->refresh($rana);

        $lifecycles = $this->ranaLifeCycleRepository->findBy([
            "rana" => $rana,
            "currentTimeslot" => $timeslot
        ]);
        $all_approved = true;
        foreach ($lifecycles as $lifecycle) {
            if ($lifecycle->getCurrentState() !== "APPR") {
                $all_approved = false;
            }
        }
        if ($all_approved) {
            $randa->setCurrentState("TODO");
            $this->randaRepository->save($randa);
        }


        return new JsonResponse($this->ranaFormatter->formatData($rana, $role, $all_approved, $randa->getCurrentTimeslot(), null));
    }


    /**
     * Get the chapter's rana
     *
     * @Route(path="/{id}/rana", name="get_rana", methods={"GET"})
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
     *      description="Returns a Rana object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(
     *              property="newMembers",
     *              type="object",
     *              @SWG\Property(
     *                  property="APPR",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="CONS",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="PROP",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              )
     *          ),
     *          @SWG\Property(
     *              property="renewedMembers",
     *              type="object",
     *              @SWG\Property(
     *                  property="APPR",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="CONS",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="PROP",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              )
     *          ),
     *          @SWG\Property(
     *              property="retention",
     *              type="object",
     *              @SWG\Property(
     *                  property="APPR",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="CONS",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="PROP",
     *                  type="object",
     *                  @SWG\Property(property="m1", type="integer"),
     *                  @SWG\Property(property="m2", type="integer"),
     *                  @SWG\Property(property="m3", type="integer"),
     *                  @SWG\Property(property="m4", type="integer"),
     *                  @SWG\Property(property="m5", type="integer"),
     *                  @SWG\Property(property="m6", type="integer"),
     *                  @SWG\Property(property="m7", type="integer"),
     *                  @SWG\Property(property="m8", type="integer"),
     *                  @SWG\Property(property="m9", type="integer"),
     *                  @SWG\Property(property="m10", type="integer"),
     *                  @SWG\Property(property="m11", type="integer"),
     *                  @SWG\Property(property="m12", type="integer")
     *              )
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
     *      description="Returned if actAs is given but is not a valid user id or if there are any rana associated to the specified chapter."
     * )
     * @SWG\Tag(name="Rana")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function getRana(Chapter $chapter, Request $request): Response
    {
        $region = $chapter->getRegion();

        $timeslot = null;
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

        if ($code == Response::HTTP_OK && !$isAdmin) {
            if ($role == Constants::ROLE_ASSISTANT && $chapter->getDirector() != $director) {
                $code = Response::HTTP_FORBIDDEN;
            }

            if ($role == Constants::ROLE_AREA) {
                if($chapter->getDirector()->getSupervisor() != $director && $chapter->getDirector() !== $director) {
                    $code = Response::HTTP_FORBIDDEN;
                }
            }
        }

        if ($code == Response::HTTP_OK) {
            $randa = $this->randaRepository->findOneBy([
                'region' => $region,
                'year' => date('Y')
            ]);

            if (is_null($randa)) {
                $code = Response::HTTP_NOT_FOUND;
            } else {
                $rana = $this->ranaRepository->findOneBy([
                    'randa' => $randa,
                    'chapter' => $chapter
                ]);
                if (is_null($rana)) {
                    $code = Response::HTTP_NOT_FOUND;
                }
            }
        }

        if ($code == Response::HTTP_OK) {
            return new JsonResponse($this->ranaFormatter->formatData($rana, $role, false, $timeslot = $randa->getCurrentTimeslot(), $randa->getRefuseNote()));
        } else {
            return new JsonResponse(null, $code);
        }
    }


    /**
     * Create or update a members block
     *
     * @Route(path="/{id}/disapprove", name="disapprove_rana", methods={"PUT"})
     *
     * @return Response
     */
    public function disapproveRana(Rana $rana, Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $chapter = $rana->getChapter();

        $lifecycle = $this->ranaLifeCycleRepository->findOneBy([
            "rana" => $rana,
            "currentTimeslot" => $rana->getRanda()->getCurrentTimeslot(),
            "currentState" => "APPR"
        ]);
        if ($lifecycle) {
            $lifecycle->setCurrentState("PROP");
            $this->ranaLifeCycleRepository->save($lifecycle);
        }

        $retention = $this->retentionRepository->findOneBy([
            "rana" => $rana,
            "valueType" => "APPR",
            "timeslot" => $rana->getRanda()->getCurrentTimeslot()
        ]);
        if ($retention) {
            $this->entityManager->remove($retention);
            $this->entityManager->flush();
        } else {
            return new JsonResponse("NOT FOUND");
        }

        $new_member = $this->newMemberRepository->findOneBy([
            "rana" => $rana,
            "valueType" => "APPR",
            "timeslot" => $rana->getRanda()->getCurrentTimeslot()
        ]);
        $this->entityManager->remove($new_member);
        $this->entityManager->flush();

        $randa = $rana->getRanda();
        $randa->setCurrentState("TODO");
        $this->randaRepository->save($randa);
        return new JsonResponse($this->ranaFormatter->formatData($rana, null, false,  $rana->getRanda()->getCurrentTimeslot(), null));
    }
}
