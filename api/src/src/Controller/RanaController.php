<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Rana;
use App\Entity\RanaLifecycle;
use App\Formatter\RanaFormatter;
use App\Repository\DirectorRepository;
use App\Repository\RanaLifecycleRepository;
use App\Repository\RanaRepository;
use App\Repository\RandaRepository;
use App\Repository\UserRepository;
use App\Util\Util;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RanaController extends AbstractController
{
    /** @var DirectorRepository */
    private $directorRepository;

    /** @var RanaFormatter */
    private $ranaFormatter;

    /** @var RanaLifecycleRepository */
    private $ranaLifeCycleRepository;

    /** @var RanaRepository */
    private $ranaRepository;

    /** @var RandaRepository */
    private $randaRepository;

    /** @var UserRepository */
    private $userRepository;
    

    public function __construct(
        DirectorRepository $directorRepository,
        RanaFormatter $ranaFormatter,
        RanaLifecycleRepository $ranaLifeCycleRepository,
        RanaRepository $ranaRepository,
        RandaRepository $randaRepository,
        UserRepository $userRepository
    ) {
        $this->directorRepository = $directorRepository;
        $this->ranaFormatter = $ranaFormatter;
        $this->ranaLifeCycleRepository = $ranaLifeCycleRepository;
        $this->ranaRepository = $ranaRepository;
        $this->randaRepository = $randaRepository;
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
    public function createRanda(Chapter $chapter, Request $request): Response
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

        $region = $chapter-> getRegion();
        if ($code == Response::HTTP_OK && !$isAdmin) {
            $checkDirectorRole = $this->directorRepository->checkDirectorRole($user, $region, $role);

            $code = Util::arrayGetValue($checkDirectorRole, 'code', $code);
            $director = Util::arrayGetValue($checkDirectorRole, 'director', null);
            $role = $director ? $director->getRole() : null;
        }

        if ($code == Response::HTTP_OK) {
            $role = $isAdmin ? $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE : $role;
            if (!in_array($role, [
                $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE,
                $this->directorRepository::DIRECTOR_ROLE_AREA,
                $this->directorRepository::DIRECTOR_ROLE_ASSISTANT
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
            $ranaLifeCycle->setCurrentState($this->ranaLifeCycleRepository::RANA_LIFECYCLE_STATUS_TODO);
            $ranaLifeCycle->setCurrentTimeslot($this->ranaLifeCycleRepository::RANA_LIFECYCLE_CURRENT_TIMESLOT_T0);
            $ranaLifeCycle->setRana($rana);
            $this->ranaLifeCycleRepository->save($ranaLifeCycle);

            return new JsonResponse($this->ranaFormatter->formatBase($rana));
        } else {
            return new JsonResponse(null, $code);
        }
    }
}
