<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\NewMember;
use App\Entity\Rana;
use App\Entity\RanaLifecycle;
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
    /** @var Constants */
    private $constants;

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

    public function __construct(
        Constants $constants,
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
        $this->constants = $constants;
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
     *              property="chapter",
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
     *          ),
     *          @SWG\Property(
     *              property="randa",
     *              type="object",
     *              @SWG\Property(property="id", type="string"),
     *              @SWG\Property(property="year", type="integer"),
     *              @SWG\Property(
     *                  property="region",
     *                  type="object",
     *                  @SWG\Property(property="id", type="string"),
     *                  @SWG\Property(property="name", type="string")
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

        $actAs = $request->get("actAs");
        $code = Response::HTTP_OK;
        $role = $request->get("role");
        $user = $this->getUser();
        $isAdmin = $user->isAdmin() && is_null($actAs);

        $checkUser = $this->userRepository->checkUser($user, $actAs);
        $user = Util::arrayGetValue($checkUser, 'user');
        $code = Util::arrayGetValue($checkUser, 'code');

        $region = $chapter->getRegion();
        if ($code == Response::HTTP_OK && !$isAdmin) {
            $checkDirectorRole = $this->directorRepository->checkDirectorRole($user, $region, $role);

            $code = Util::arrayGetValue($checkDirectorRole, 'code', $code);
            $director = Util::arrayGetValue($checkDirectorRole, 'director', null);
            $role = $director ? $director->getRole() : null;
        }

        if ($code == Response::HTTP_OK) {
            $role = $isAdmin ? $this->constants::ROLE_EXECUTIVE : $role;
            if (!in_array($role, [
                $this->constants::ROLE_EXECUTIVE,
                $this->constants::ROLE_AREA,
                $this->constants::ROLE_ASSISTANT
            ])) {
                $code = Response::HTTP_FORBIDDEN;
            }
        }

        if ($code == Response::HTTP_OK) {
            $currentYear = (int) date("Y");
            $randa = $this->randaRepository->findOneBy([
                'region' => $region,
                'year' => $currentYear
            ]);

            if (is_null($randa)) {
                $this->forward('App\Controller\RandaController::createRanda', [
                    'region'  => $region
                ])->getContent();

                $randa = $this->randaRepository->findOneBy([
                    'region' => $region,
                    'year' => $currentYear
                ]);
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
            $ranaLifeCycle->setCurrentState($this->constants::RANA_LIFECYCLE_STATUS_TODO);
            $ranaLifeCycle->setCurrentTimeslot($this->constants::TIMESLOT_T0);
            $ranaLifeCycle->setRana($rana);
            $this->ranaLifeCycleRepository->save($ranaLifeCycle);

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

        $actAs = $request->get("actAs");
        $code = Response::HTTP_OK;
        $role = $request->get("role");
        $user = $this->getUser();
        $isAdmin = $user->isAdmin() && is_null($actAs);

        $checkUser = $this->userRepository->checkUser($user, $actAs);
        $user = Util::arrayGetValue($checkUser, 'user');
        $code = Util::arrayGetValue($checkUser, 'code');

        $chapter = $rana->getChapter();
        $region = $chapter->getRegion();

        if ($code == Response::HTTP_OK && !$isAdmin) {
            $checkDirectorRole = $this->directorRepository->checkDirectorRole($user, $region, $role);

            $code = Util::arrayGetValue($checkDirectorRole, 'code', $code);
            $director = Util::arrayGetValue($checkDirectorRole, 'director', null);
            $role = $director ? $director->getRole() : $role;
        }

        if ($code == Response::HTTP_OK) {
            $role = $isAdmin ? $this->constants::ROLE_EXECUTIVE : $role;
            if (!in_array($role, [
                $this->constants::ROLE_EXECUTIVE,
                $this->constants::ROLE_AREA,
                $this->constants::ROLE_ASSISTANT
            ])) {
                $code = Response::HTTP_FORBIDDEN;
            }
        }

        if ($code == Response::HTTP_OK && !$isAdmin) {
            if ($role == $this->constants::ROLE_ASSISTANT && $chapter->getDirector() != $director) {
                $code = Response::HTTP_FORBIDDEN;
            }

            if ($role == $this->constants::ROLE_AREA && $chapter->getDirector()->getSupervisor() != $director) {
                $code = Response::HTTP_FORBIDDEN;
            }
        }

        if ($code == Response::HTTP_OK) {
            $timeslot = $request->get("timeslot");
            $valueType = $request->get("valueType");
            $errorFields = [];

            $availableTimeslots = [
                $this->constants::TIMESLOT_T0,
                $this->constants::TIMESLOT_T1,
                $this->constants::TIMESLOT_T2,
                $this->constants::TIMESLOT_T3,
                $this->constants::TIMESLOT_T4
            ];
            $availableValueTypes = [
                $this->constants::VALUE_TYPE_APPROVED,
                $this->constants::VALUE_TYPE_CONSUMPTIVE,
                $this->constants::VALUE_TYPE_PROPOSED
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
                        $this->constants::VALUE_TYPE_APPROVED,
                        $this->constants::VALUE_TYPE_CONSUMPTIVE
                    ]
                ]);

                $nextRetentions = $this->retentionRepository->findBy([
                    'rana' => $rana,
                    'timeslot' => $nextTimeslot,
                    'valueType' => [
                        $this->constants::VALUE_TYPE_APPROVED,
                        $this->constants::VALUE_TYPE_CONSUMPTIVE
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
                        case $this->constants::VALUE_TYPE_CONSUMPTIVE:
                            if ($timeslot == $this->constants::TIMESLOT_T0) {
                                $errorFields['valueType'] = "invalid";
                            }
                            break;
                        case $this->constants::VALUE_TYPE_APPROVED:
                            if ($role != $this->constants::ROLE_EXECUTIVE) {
                                $code = Response::HTTP_FORBIDDEN;
                            } elseif ($timeslot == $this->constants::TIMESLOT_T4) {
                                $errorFields['valueType'] = "invalid";
                            } else {
                                $lastRetentions = $this->retentionRepository->findOneBy([
                                    'rana' => $rana,
                                    'timeslot' => $timeslot,
                                    'valueType' => $this->constants::VALUE_TYPE_CONSUMPTIVE
                                ]);

                                $lastNewMembers = $this->newMemberRepository->findOneBy([
                                    'rana' => $rana,
                                    'timeslot' => $timeslot,
                                    'valueType' => $this->constants::VALUE_TYPE_CONSUMPTIVE
                                ]);

                                if (!is_null($lastRetentions) || !is_null($lastNewMembers)) {
                                    $errorFields['valueType'] = "invalid";
                                }
                            }

                            break;
                        case $this->constants::VALUE_TYPE_PROPOSED:
                            if ($timeslot == $this->constants::TIMESLOT_T4) {
                                $errorFields['valueType'] = "invalid";
                            } else {
                                $lastRetentions = $this->retentionRepository->findBy([
                                    'rana' => $rana,
                                    'timeslot' => $timeslot,
                                    'valueType' => [
                                        $this->constants::VALUE_TYPE_CONSUMPTIVE,
                                        $this->constants::VALUE_TYPE_APPROVED
                                    ]
                                ]);

                                $lastNewMembers = $this->newMemberRepository->findBy([
                                    'rana' => $rana,
                                    'timeslot' => $timeslot,
                                    'valueType' => [
                                        $this->constants::VALUE_TYPE_CONSUMPTIVE,
                                        $this->constants::VALUE_TYPE_APPROVED
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

            if ($valueType == $this->constants::VALUE_TYPE_CONSUMPTIVE) {
                switch ($timeslot) {
                    case $this->constants::TIMESLOT_T4:
                        $previous = $this->newMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T3
                        ]);
                        $newMember->setM12($request->get("n_m12") ?? ($previous ? $previous->getM12() : 0));
                        $newMember->setM11($request->get("n_m11") ?? ($previous ? $previous->getM11() : 0));
                        $newMember->setM10($request->get("n_m10") ?? ($previous ? $previous->getM10() : 0));

                        $previous = $this->retentionRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T3
                        ]);
                        $retentionMember->setM12($request->get("r_m12") ?? ($previous ? $previous->getM12() : 0));
                        $retentionMember->setM11($request->get("r_m11") ?? ($previous ? $previous->getM11() : 0));
                        $retentionMember->setM10($request->get("r_m10") ?? ($previous ? $previous->getM10() : 0));
                    case $this->constants::TIMESLOT_T3:
                        $previous = $this->newMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T2
                        ]);
                        $newMember->setM9($request->get("n_m9") ?? ($previous ? $previous->getM9() : 0));
                        $newMember->setM8($request->get("n_m8") ?? ($previous ? $previous->getM8() : 0));
                        $newMember->setM7($request->get("n_m7") ?? ($previous ? $previous->getM7() : 0));

                        $previous = $this->retentionRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T2
                        ]);
                        $retentionMember->setM9($request->get("r_m9") ?? ($previous ? $previous->getM9() : 0));
                        $retentionMember->setM8($request->get("r_m8") ?? ($previous ? $previous->getM8() : 0));
                        $retentionMember->setM7($request->get("r_m7") ?? ($previous ? $previous->getM7() : 0));
                    case $this->constants::TIMESLOT_T2:
                        $previous = $this->newMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T1
                        ]);
                        $newMember->setM6($request->get("n_m6") ?? ($previous ? $previous->getM6() : 0));
                        $newMember->setM5($request->get("n_m5") ?? ($previous ? $previous->getM5() : 0));
                        $newMember->setM4($request->get("n_m4") ?? ($previous ? $previous->getM4() : 0));

                        $previous = $this->retentionRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T1
                        ]);
                        $retentionMember->setM6($request->get("r_m6") ?? ($previous ? $previous->getM6() : 0));
                        $retentionMember->setM5($request->get("r_m5") ?? ($previous ? $previous->getM5() : 0));
                        $retentionMember->setM4($request->get("r_m4") ?? ($previous ? $previous->getM4() : 0));
                    case $this->constants::TIMESLOT_T1:
                        $newMember->setM3($request->get("n_m3") ?? 0);
                        $newMember->setM2($request->get("n_m2") ?? 0);
                        $newMember->setM1($request->get("n_m1") ?? 0);

                        $retentionMember->setM3($request->get("r_m3") ?? 0);
                        $retentionMember->setM2($request->get("r_m2") ?? 0);
                        $retentionMember->setM1($request->get("r_m1") ?? 0);
                }
            } else {
                switch ($timeslot) {
                    case $this->constants::TIMESLOT_T0:
                        $newMember->setM1($request->get("n_m1") ?? 0);
                        $newMember->setM2($request->get("n_m2") ?? 0);
                        $newMember->setM3($request->get("n_m3") ?? 0);

                        $retentionMember->setM1($request->get("r_m1") ?? 0);
                        $retentionMember->setM2($request->get("r_m2") ?? 0);
                        $retentionMember->setM3($request->get("r_m3") ?? 0);
                    case $this->constants::TIMESLOT_T1:
                        $previousNewMember = $this->newMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T0
                        ]);
                        $newMember->setM4($request->get("n_m4") ?? ($previousNewMember ? $previousNewMember->getM4() : 0));
                        $newMember->setM5($request->get("n_m5") ?? ($previousNewMember ? $previousNewMember->getM5() : 0));
                        $newMember->setM6($request->get("n_m6") ?? ($previousNewMember ? $previousNewMember->getM6() : 0));

                        $previousRetentionMember = $this->retentionRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T0
                        ]);
                        $retentionMember->setM6($request->get("r_m6") ?? ($previousRetentionMember ? $previousRetentionMember->getM6() : 0));
                        $retentionMember->setM4($request->get("r_m4") ?? ($previousRetentionMember ? $previousRetentionMember->getM4() : 0));
                        $retentionMember->setM5($request->get("r_m5") ?? ($previousRetentionMember ? $previousRetentionMember->getM5() : 0));
                    case $this->constants::TIMESLOT_T2:
                        $previousNewMember = $this->newMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T1
                        ]);
                        $newMember->setM7($request->get("n_m7") ?? ($previousNewMember ? $previousNewMember->getM7() : 0));
                        $newMember->setM8($request->get("n_m8") ?? ($previousNewMember ? $previousNewMember->getM8() : 0));
                        $newMember->setM9($request->get("n_m9") ?? ($previousNewMember ? $previousNewMember->getM9() : 0));

                        $previousRetentionMember = $this->retentionRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T1
                        ]);
                        $retentionMember->setM7($request->get("r_m7") ?? ($previousRetentionMember ? $previousRetentionMember->getM7() : 0));
                        $retentionMember->setM8($request->get("r_m8") ?? ($previousRetentionMember ? $previousRetentionMember->getM8() : 0));
                        $retentionMember->setM9($request->get("r_m9") ?? ($previousRetentionMember ? $previousRetentionMember->getM9() : 0));
                    case $this->constants::TIMESLOT_T3:
                        $previousNewMember = $this->newMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T2
                        ]);
                        $newMember->setM10($request->get("n_m10") ?? ($previousNewMember ? $previousNewMember->getM10() : 0));
                        $newMember->setM11($request->get("n_m11") ?? ($previousNewMember ? $previousNewMember->getM11() : 0));
                        $newMember->setM12($request->get("n_m12") ?? ($previousNewMember ? $previousNewMember->getM12() : 0));

                        $previousRetentionMember = $this->retentionRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T2
                        ]);
                        $retentionMember->setM10($request->get("r_m10") ?? ($previousRetentionMember ? $previousRetentionMember->getM10() : 0));
                        $retentionMember->setM11($request->get("r_m11") ?? ($previousRetentionMember ? $previousRetentionMember->getM11() : 0));
                        $retentionMember->setM12($request->get("r_m12") ?? ($previousRetentionMember ? $previousRetentionMember->getM12() : 0));
                }
            }

            if ($isNewNewMember) {
                $this->newMemberRepository->save($newMember);
            }

            if ($isNewRetentionMember) {
                $this->retentionRepository->save($retentionMember);
            }

            $status = $this->constants::RANA_LIFECYCLE_STATUS_TODO;
            switch ($valueType) {
                case $this->constants::VALUE_TYPE_APPROVED:
                    $status = $this->constants::RANA_LIFECYCLE_STATUS_APPROVED;
                    break;
                case $this->constants::VALUE_TYPE_PROPOSED:
                    $status = $this->constants::RANA_LIFECYCLE_STATUS_PROPOSED;
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

                $status = $this->constants::RANA_LIFECYCLE_STATUS_TODO;
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
        $actAs = $request->get("actAs");
        $code = Response::HTTP_OK;
        $role = $request->get("role");
        $user = $this->getUser();
        $isAdmin = $user->isAdmin() && is_null($actAs);

        $checkUser = $this->userRepository->checkUser($user, $actAs);
        $user = Util::arrayGetValue($checkUser, 'user');
        $code = Util::arrayGetValue($checkUser, 'code');

        $region = $chapter->getRegion();

        if ($code == Response::HTTP_OK && !$isAdmin) {
            $checkDirectorRole = $this->directorRepository->checkDirectorRole($user, $region, $role);

            $code = Util::arrayGetValue($checkDirectorRole, 'code', $code);
            $director = Util::arrayGetValue($checkDirectorRole, 'director', null);
            $role = $director ? $director->getRole() : $role;
        }

        if ($code == Response::HTTP_OK) {
            $role = $isAdmin ? $this->constants::ROLE_EXECUTIVE : $role;
            if (!in_array($role, [
                $this->constants::ROLE_EXECUTIVE,
                $this->constants::ROLE_AREA,
                $this->constants::ROLE_ASSISTANT
            ])) {
                $code = Response::HTTP_FORBIDDEN;
            }
        }

        if ($code == Response::HTTP_OK && !$isAdmin) {
            if ($role == $this->constants::ROLE_ASSISTANT && $chapter->getDirector() != $director) {
                $code = Response::HTTP_FORBIDDEN;
            }

            if ($role == $this->constants::ROLE_AREA && $chapter->getDirector()->getSupervisor() != $director) {
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
