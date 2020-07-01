<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\NewMember;
use App\Entity\Rana;
use App\Entity\RanaLifecycle;
use App\Entity\Randa;
use App\Entity\RenewedMember;
use App\Entity\Retention;
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
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    /** @var RenewedMemberRepository */
    private $renewedMemberRepository;

    /** @var RetentionRepository */
    private $retentionRepository;

    /** @var UserRepository */
    private $userRepository;

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
        UserRepository $userRepository
    ) {
        $this->directorRepository = $directorRepository;
        $this->entityManager = $entityManager;
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

            return new JsonResponse($this->ranaFormatter->formatData($rana, $role));
        } else {
            return new JsonResponse(null, $code);
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

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
        }

        if ($code == Response::HTTP_OK && !$isAdmin) {
            if ($role == Constants::ROLE_ASSISTANT && $chapter->getDirector() != $director) {
                $code = Response::HTTP_FORBIDDEN;
            }

            if ($role == Constants::ROLE_AREA && $chapter->getDirector()->getSupervisor() != $director) {
                $code = Response::HTTP_FORBIDDEN;
            }
        }

        if ($code == Response::HTTP_OK) {
            $timeslot = $request->get("timeslot");
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
                Constants::VALUE_TYPE_APPROVED,
                Constants::VALUE_TYPE_CONSUMPTIVE,
                Constants::VALUE_TYPE_PROPOSED
            ];

            if (!in_array($timeslot, $availableTimeslots)) {
                $errorFields['timeslot'] = "invalid";
            } else {
                $slotNumber = (int) substr($timeslot, -1);
                $nextSlotNumber = $slotNumber + 1;
                $nextTimeslot = "T$nextSlotNumber";

                // Verifico che non ci sia un timeslot successivo in stato approvato o consuntivo
                $nextNewMembers = $this->newMemberRepository->findBy([
                    'rana' => $rana,
                    'timeslot' => $nextTimeslot,
                    'valueType' => [
                        Constants::VALUE_TYPE_APPROVED,
                        Constants::VALUE_TYPE_CONSUMPTIVE
                    ]
                ]);

                $nextRetentions = $this->retentionRepository->findBy([
                    'rana' => $rana,
                    'timeslot' => $nextTimeslot,
                    'valueType' => [
                        Constants::VALUE_TYPE_APPROVED,
                        Constants::VALUE_TYPE_CONSUMPTIVE
                    ]
                ]);

                if (!empty($nextRetentions) || !empty($nextNewMembers)) {
                    $errorFields['timeslot'] = "invalid";
                }

                if ($slotNumber > 1) {
                    // Verifico che esista il timeslot precedente
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
                        case Constants::VALUE_TYPE_APPROVED:
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
                        case Constants::VALUE_TYPE_PROPOSED:
                            if ($timeslot == Constants::TIMESLOT_T4) {
                                $errorFields['valueType'] = "invalid";
                            } else {
                                $lastRetentions = $this->retentionRepository->findBy([
                                    'rana' => $rana,
                                    'timeslot' => $timeslot,
                                    'valueType' => [
                                        Constants::VALUE_TYPE_CONSUMPTIVE,
                                        Constants::VALUE_TYPE_APPROVED
                                    ]
                                ]);

                                $lastNewMembers = $this->newMemberRepository->findBy([
                                    'rana' => $rana,
                                    'timeslot' => $timeslot,
                                    'valueType' => [
                                        Constants::VALUE_TYPE_CONSUMPTIVE,
                                        Constants::VALUE_TYPE_APPROVED
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

            if ($code == Response::HTTP_OK && !empty($errorFields)) {
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

        if ($code == Response::HTTP_OK) {
            $isNewNewMember = false;
            $isNewRenewedMember = false;
            $isNewRetentionMember = false;

            // Recupero o creo i newMembers per questo timeslot e questo valueType e setto i dati
            $newMember = $this->newMemberRepository->findOneBy([
                'rana' => $rana,
                'timeslot' => $timeslot,
                'valueType' => $valueType
            ]);

            if (is_null($newMember)) {
                $newMember = new NewMember();
                $isNewNewMember = true;
            }

            $newMember->setRana($rana);
            $newMember->setTimeslot($timeslot);
            $newMember->setValueType($valueType);

            // Recupero o creo i retentionMembers per questo timeslot e questo valueType e setto i dati
            $retentionMember = $this->retentionRepository->findOneBy([
                'rana' => $rana,
                'timeslot' => $timeslot,
                'valueType' => $valueType
            ]);

            if (is_null($retentionMember)) {
                $retentionMember = new Retention();
                $isNewRetentionMember = true;
            }

            $retentionMember->setRana($rana);
            $retentionMember->setTimeslot($timeslot);
            $retentionMember->setValueType($valueType);

            // Recupero o creo i renewedMembers per questo timeslot e questo valueType e setto i dati
            $renewedMember = $this->renewedMemberRepository->findOneBy([
                'rana' => $rana,
                'timeslot' => $timeslot,
                'valueType' => $valueType
            ]);

            if (is_null($renewedMember)) {
                $renewedMember = new RenewedMember();
                $isNewRenewedMember = true;
            }

            $renewedMember->setRana($rana);
            $renewedMember->setTimeslot($timeslot);
            $renewedMember->setValueType($valueType);

            $memberValues = $this->ranaRepository->getMembersQuantities($rana, $valueType, $timeslot);

            foreach ($memberValues as $var => $data) {
                switch ($var) {
                    case 'newMember':
                        $prefix = "n_";
                    break;
                    case 'retentionMember':
                        $prefix = "r_";
                    break;
                }
                foreach ($data as $slot => $value) {
                    $method = "set" . strtoupper($slot);
                    $startValue = in_array($var, [
                        'newMember',
                        'retentionMember'
                    ]) ? $request->get("$prefix$slot") : null;
                    $$var->$method($startValue ?? $value);
                }
            }

            if ($isNewNewMember) {
                $this->newMemberRepository->save($newMember);
            }

            if ($isNewRenewedMember) {
                $this->renewedMemberRepository->save($renewedMember);
            }

            if ($isNewRetentionMember) {
                $this->retentionRepository->save($retentionMember);
            }

            $status = Constants::RANA_LIFECYCLE_STATUS_TODO;
            switch ($valueType) {
                case Constants::VALUE_TYPE_APPROVED:
                    $status = Constants::RANA_LIFECYCLE_STATUS_APPROVED;
                    break;
                case Constants::VALUE_TYPE_PROPOSED:
                    $status = Constants::RANA_LIFECYCLE_STATUS_PROPOSED;
                    break;
            }

            $currentLifeCycle = $this->ranaLifeCycleRepository->findOneBy([
                "rana" => $rana,
                "currentTimeslot" => $timeslot
            ]);

            //if lifecycle exists for this rana, update status and create a new lifecycle record with todo state for next t
            if ($currentLifeCycle) {
                $currentLifeCycle->setCurrentState($status);

                $this->entityManager->flush();

                $slotNumber = (int) substr($timeslot, -1);
                if ($slotNumber < 4) {
                    $nextSlotNumber = $slotNumber + 1;
                    $nextTimeslot = "T$nextSlotNumber";
                }

                $status = Constants::RANA_LIFECYCLE_STATUS_TODO;
            } else {
                $nextTimeslot = $timeslot;
            }

            $ranaLifeCycle = new RanaLifecycle();
            $ranaLifeCycle->setCurrentState($status);
            $ranaLifeCycle->setCurrentTimeslot($nextTimeslot);
            $ranaLifeCycle->setRana($rana);

            $this->ranaLifeCycleRepository->save($ranaLifeCycle);
            $this->entityManager->refresh($rana);

            return new JsonResponse($this->ranaFormatter->formatData($rana, $role));
        } else {
            $errorFields = $code == Response::HTTP_BAD_REQUEST ? $errorFields : null;
            return new JsonResponse($errorFields, $code);
        }
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

            if ($role == Constants::ROLE_AREA && $chapter->getDirector()->getSupervisor() != $director) {
                $code = Response::HTTP_FORBIDDEN;
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
            return new JsonResponse($this->ranaFormatter->formatData($rana, $role));
        } else {
            return new JsonResponse(null, $code);
        }
    }
}
