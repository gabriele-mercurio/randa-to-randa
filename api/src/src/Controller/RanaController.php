<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Rana;
use App\Entity\RanaLifecycle;
use App\Entity\RenewedMember;
use App\Formatter\RanaFormatter;
use App\Repository\DirectorRepository;
use App\Repository\RanaLifecycleRepository;
use App\Repository\RanaRepository;
use App\Repository\RandaRepository;
use App\Repository\RenewedMemberRepository;
use App\Repository\UserRepository;
use App\Util\Constants;
use App\Util\Util;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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

    /** @var RenewedMemberRepository */
    private $renewedMemberRepository;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(
        Constants $constants,
        DirectorRepository $directorRepository,
        EntityManagerInterface $entityManager,
        RanaFormatter $ranaFormatter,
        RanaLifecycleRepository $ranaLifeCycleRepository,
        RanaRepository $ranaRepository,
        RandaRepository $randaRepository,
        RenewedMemberRepository $renewedMemberRepository,
        UserRepository $userRepository
    ) {
        $this->constants = $constants;
        $this->directorRepository = $directorRepository;
        $this->entityManager = $entityManager;
        $this->ranaFormatter = $ranaFormatter;
        $this->ranaLifeCycleRepository = $ranaLifeCycleRepository;
        $this->ranaRepository = $ranaRepository;
        $this->randaRepository = $randaRepository;
        $this->renewedMemberRepository = $renewedMemberRepository;
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
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

        if ($code == Response::HTTP_OK) {
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

            return new JsonResponse($this->ranaFormatter->formatBase($rana));
        } else {
            return new JsonResponse(null, $code);
        }
    }

    /**
     * Create or update a renewed members block
     *
     * @Route(path="/{id}/rana-renewed", name="manage_rana_renewed", methods={"POST"})
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
     *      name="m1",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="m2",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="m3",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="m4",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="m5",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="m6",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="m7",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="m8",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="m9",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="m10",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="m11",
     *      in="formData",
     *      type="integer"
     * )
     * @SWG\Parameter(
     *      name="m12",
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
    public function createRenuwedMembers(Rana $rana, Request $request): Response
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
                $nextRenewedMembers = $this->renewedMemberRepository->findBy([
                    'rana' => $rana,
                    'timeslot' => $nextTimeslot,
                    'valueType' => [
                        $this->constants::VALUE_TYPE_APPROVED,
                        $this->constants::VALUE_TYPE_CONSUMPTIVE
                    ]
                ]);

                if (!empty($nextRenewedMembers)) {
                    $errorFields['timeslot'] = "invalid";
                }

                if ($slotNumber && $slotNumber > 1) {
                    $prevSlotNumber = $slotNumber - 1;
                    $prevTimeslot = "T$prevSlotNumber";
                    $prevRenewedMembers = $this->renewedMemberRepository->findBy([
                        'rana' => $rana,
                        'timeslot' => $prevTimeslot
                    ]);

                    if (empty($prevRenewedMembers)) {
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
                            } else {
                                $lastRenewed = $this->renewedMemberRepository->findOneBy([
                                    'rana' => $rana,
                                    'timeslot' => $timeslot,
                                    'valueType' => $this->constants::VALUE_TYPE_CONSUMPTIVE
                                ]);
                                if (!is_null($lastRenewed)) {
                                    $errorFields['valueType'] = "invalid";
                                }
                            }
                            if ($timeslot == $this->constants::TIMESLOT_T4) {
                                $errorFields['valueType'] = "invalid";
                            }
                            break;
                        case $this->constants::VALUE_TYPE_PROPOSED:
                            $lastRenewed = $this->renewedMemberRepository->findBy([
                                'rana' => $rana,
                                'timeslot' => $timeslot,
                                'valueType' => [
                                    $this->constants::VALUE_TYPE_CONSUMPTIVE,
                                    $this->constants::VALUE_TYPE_APPROVED
                                ]
                            ]);
                            if (!empty($lastRenewed)) {
                                $errorFields['valueType'] = "invalid";
                            }
                            if ($timeslot == $this->constants::TIMESLOT_T4) {
                                $errorFields['valueType'] = "invalid";
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
            $isNew = false;
            $renewedMembers = $this->renewedMemberRepository->findOneBy([
                'rana' => $rana,
                'timeslot' => $timeslot,
                'valueType' => $valueType
            ]);
            if (is_null($renewedMembers)) {
                $renewedMembers = new RenewedMember();
                $isNew = true;
            }
            $renewedMembers->setRana($rana);
            $renewedMembers->setTimeslot($timeslot);
            $renewedMembers->setValueType($valueType);

            if ($valueType == $this->constants::VALUE_TYPE_CONSUMPTIVE) {
                switch ($timeslot) {
                    case $this->constants::TIMESLOT_T4:
                        $previous = $this->renewedMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T3
                        ]);
                        $renewedMembers->setM12($request->get("m12") ?? ($previous ? $previous->getM12() : 0));
                        $renewedMembers->setM11($request->get("m11") ?? ($previous ? $previous->getM11() : 0));
                        $renewedMembers->setM10($request->get("m10") ?? ($previous ? $previous->getM10() : 0));
                    case $this->constants::TIMESLOT_T3:
                        $previous = $this->renewedMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T2
                        ]);
                        $renewedMembers->setM9($request->get("m9") ?? ($previous ? $previous->getM9() : 0));
                        $renewedMembers->setM8($request->get("m8") ?? ($previous ? $previous->getM8() : 0));
                        $renewedMembers->setM7($request->get("m7") ?? ($previous ? $previous->getM7() : 0));
                    case $this->constants::TIMESLOT_T2:
                        $previous = $this->renewedMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T1
                        ]);
                        $renewedMembers->setM6($request->get("m6") ?? ($previous ? $previous->getM6() : 0));
                        $renewedMembers->setM5($request->get("m5") ?? ($previous ? $previous->getM5() : 0));
                        $renewedMembers->setM4($request->get("m4") ?? ($previous ? $previous->getM4() : 0));
                    case $this->constants::TIMESLOT_T1:
                        $renewedMembers->setM3($request->get("m3") ?? 0);
                        $renewedMembers->setM2($request->get("m2") ?? 0);
                        $renewedMembers->setM1($request->get("m1") ?? 0);
                }
            } else {
                switch ($timeslot) {
                    case $this->constants::TIMESLOT_T0:
                        $renewedMembers->setM1($request->get("m1") ?? 0);
                        $renewedMembers->setM2($request->get("m2") ?? 0);
                        $renewedMembers->setM3($request->get("m3") ?? 0);
                    case $this->constants::TIMESLOT_T1:
                        $previous = $this->renewedMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T0
                        ]);
                        $renewedMembers->setM4($request->get("m4") ?? ($previous ? $previous->getM4() : 0));
                        $renewedMembers->setM5($request->get("m5") ?? ($previous ? $previous->getM5() : 0));
                        $renewedMembers->setM6($request->get("m6") ?? ($previous ? $previous->getM6() : 0));
                    case $this->constants::TIMESLOT_T2:
                        $previous = $this->renewedMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T1
                        ]);
                        $renewedMembers->setM7($request->get("m7") ?? ($previous ? $previous->getM7() : 0));
                        $renewedMembers->setM8($request->get("m8") ?? ($previous ? $previous->getM8() : 0));
                        $renewedMembers->setM9($request->get("m9") ?? ($previous ? $previous->getM9() : 0));
                    case $this->constants::TIMESLOT_T3:
                        $previous = $this->renewedMemberRepository->findOneBy([
                            'rana' => $rana,
                            'valueType' => $valueType,
                            'timeslot' => $this->constants::TIMESLOT_T2
                        ]);
                        $renewedMembers->setM10($request->get("m10") ?? ($previous ? $previous->getM10() : 0));
                        $renewedMembers->setM11($request->get("m11") ?? ($previous ? $previous->getM11() : 0));
                        $renewedMembers->setM12($request->get("m12") ?? ($previous ? $previous->getM12() : 0));
                }
            }

            if ($isNew) {
                $this->renewedMemberRepository->save($renewedMembers);
            }

            $ranaLifeCycle = new RanaLifecycle();
            
            $ranaLifeCycle->setCurrentTimeslot($timeslot);

            $this->entityManager->flush();

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
