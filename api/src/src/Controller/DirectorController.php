<?php

namespace App\Controller;

use App\Entity\Director;
use App\Entity\Region;
use App\Entity\User;
use App\Formatter\DirectorFormatter;
use App\OldDB\Entity\Director as OldDirector;
use App\OldDB\Repository\DirectorRepository as OldDirectorRepository;
use App\OldDB\Entity\Region as OldRegion;
use App\OldDB\Repository\RegionRepository as OldRegionRepository;
use App\Repository\DirectorRepository;
use App\Repository\RegionRepository;
use App\Repository\UserRepository;
use App\Util\Util;
use App\Util\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DirectorController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var DirectorFormatter */
    private $directorFormatter;

    /** @var DirectorRepository */
    private $directorRepository;

    /** @var RegionRepository */
    private $regionRepository;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        DirectorFormatter $directorFormatter,
        DirectorRepository $directorRepository,
        RegionRepository $regionRepository,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->directorFormatter = $directorFormatter;
        $this->directorRepository = $directorRepository;
        $this->regionRepository = $regionRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Create a director
     * Only admin users, EXECUTIVE or NATIONAL directors can create a director
     *
     * @Route(path="/{id}/director", name="create_director", methods={"POST"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The region"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="formData",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Parameter(
     *      name="firstName",
     *      in="formData",
     *      type="string",
     *      description="The user's name",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="lastName",
     *      in="formData",
     *      type="string",
     *      description="The user's surname",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="email",
     *      in="formData",
     *      type="string",
     *      description="The user's email",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="isAdmin",
     *      in="formData",
     *      type="boolean",
     *      description="The user will be an admin?",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="role",
     *      in="formData",
     *      type="string",
     *      description="Can be one of 'ASSISTANT', 'AREA', 'EXECUTIVE' or 'NATIONAL'",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="isFreeAccount",
     *      in="formData",
     *      type="boolean",
     *      description="The new director is a free account?",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="supervisor",
     *      in="formData",
     *      type="string",
     *      description="An AREA director id. It is mandatory if we are creating an ASSISTANT director"
     * )
     * @SWG\Parameter(
     *      name="payType",
     *      in="formData",
     *      type="string",
     *      description="Can be one of 'MONTHLY' or 'ANNUAL'",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="launchPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the director takes at chapter launches"
     * )
     * @SWG\Parameter(
     *      name="greenLightPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the director takes at green light"
     * )
     * @SWG\Parameter(
     *      name="yellowLightPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the director takes at yellow light"
     * )
     * @SWG\Parameter(
     *      name="redLightPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the director takes at red light"
     * )
     * @SWG\Parameter(
     *      name="greyLightPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the director takes at grey light"
     * )
     * @SWG\Parameter(
     *      name="areaPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the area director takes"
     * )
     * @SWG\Parameter(
     *      name="fixedPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the director takes as alternative to all the other percentages"
     * )
     * @SWG\Response(
     *      response=201,
     *      description="Returns a Director object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="areaPercentage", type="integer"),
     *          @SWG\Property(property="email", type="integer"),
     *          @SWG\Property(property="firstName", type="integer"),
     *          @SWG\Property(property="fixedPercentage", type="integer"),
     *          @SWG\Property(property="fullName", type="string"),
     *          @SWG\Property(property="greenLightPercentage", type="integer"),
     *          @SWG\Property(property="greyLightPercentage", type="integer"),
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(property="lastName", type="integer"),
     *          @SWG\Property(property="launchPercentage", type="integer"),
     *          @SWG\Property(property="payType", type="string"),
     *          @SWG\Property(property="redLightPercentage", type="integer"),
     *          @SWG\Property(property="role", type="string"),
     *          @SWG\Property(
     *              property="supervisor",
     *              type="object",
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="string")
     *          ),
     *          @SWG\Property(property="yellowLightPercentage", type="integer")
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if one or more required fields are empty or if one or more fields are invalid.",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="field_name", type="string", description="The type of the error; possible values are 'required', 'in_use' or 'invalid'")
     *          )
     *      )
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin or if the user is not an admin and he/she (or the emulated user) has not EXECUTIVE or NATIONAL director rigths."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Response(
     *      response=422,
     *      description="Returned if the user exists and we found a previous association in directors for that user in the specified region and with the same role."
     * )
     * @SWG\Response(
     *      response=500,
     *      description="Returned if the server can not send an email or other 500 internal server errors."
     * )
     * @SWG\Tag(name="Directors")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function createDirector(Region $region, Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $actAsId = $request->get("actAs");
        $code = Response::HTTP_OK;
        $errorFields = [];
        $user = $this->getUser();
        $isAdmin = $user->isAdmin() && is_null($actAsId);

        $checkUser = $this->userRepository->checkUser($user, $actAsId);
        $actAs = Util::arrayGetValue($checkUser, 'user');
        $code = Util::arrayGetValue($checkUser, 'code');

        if ($code == Response::HTTP_OK && !$isAdmin) {
            $u = is_null($actAsId) ? $user : $actAs;
            $checkDirectorRole = $this->directorRepository->checkDirectorRole($u, $region);

            $code = Util::arrayGetValue($checkDirectorRole, 'code', $code);
            $director = Util::arrayGetValue($checkDirectorRole, 'director', null);
            $performerRole = $director->getRole();
        }

        if ($code == Response::HTTP_OK) {
            $performerRole = $isAdmin ? $this->directorRepository::DIRECTOR_ROLE_NATIONAL : $performerRole;
            if (!in_array($performerRole, [
                $this->directorRepository::DIRECTOR_ROLE_NATIONAL,
                $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE
            ])) {
                $code = Response::HTTP_FORBIDDEN;
            }
        }

        if ($code == Response::HTTP_OK) {
            $firstName = trim($request->get("firstName"));
            $lastName = trim($request->get("lastName"));
            $email = trim($request->get("email"));
            $isAdmin = !!(int)$request->get("isAdmin");
            $role = strtoupper(trim($request->get("role")));
            $isFreeAccount = !!(int)$request->get("isFreeAccount");
            $supervisor = $request->get("supervisor");
            $payType = strtoupper(trim($request->get("payType")));
            $launchPercentage = $request->get("launchPercentage");
            $greenLigthPercentage = $request->get("greenLightPercentage");
            $yellowLigthPercentage = $request->get("yellowLightPercentage");
            $redLigthPercentage = $request->get("redLightPercentage");
            $greyLigthPercentage = $request->get("greyLightPercentage");
            $areaPercentage = $request->get("areaPercentage");
            $fixedPercentage = $request->get("fixedPercentage");

            $availablePayTypes = [
                $this->directorRepository::DIRECTOR_PAY_TYPE_ANNUAL,
                $this->directorRepository::DIRECTOR_PAY_TYPE_MONTHLY
            ];
            $availableRoles = [
                $this->directorRepository::DIRECTOR_ROLE_AREA,
                $this->directorRepository::DIRECTOR_ROLE_ASSISTANT,
                $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE,
                $this->directorRepository::DIRECTOR_ROLE_NATIONAL
            ];
            $errorFields = [];

            if (empty($firstName)) {
                $errorFields['firstName'] = "required";
            }

            if (empty($lastName)) {
                $errorFields['lastName'] = "required";
            }

            if (empty($email)) {
                $errorFields['email'] = "required";
            } elseif (!Validator::validateEmail($email)) {
                $errorFields['email'] = "invalid";
            }

            if ((!$user->isAdmin() || !is_null($actAsId)) && $isAdmin) {
                $errorFields['isAdmin'] = "invalid";
            }

            if (empty($role)) {
                $errorFields['role'] = "required";
            } elseif (!in_array($role, $availableRoles)) {
                $errorFields['role'] = "invalid";
            } elseif ($performerRole == $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE && $role == $this->directorRepository::DIRECTOR_ROLE_NATIONAL) {
                $errorFields['role'] = "too_high";
            } elseif ($isFreeAccount && $role != $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE) {
                $errorFields['isFreeAccount'] = "invalid";
            }

            if ($role == $this->directorRepository::DIRECTOR_ROLE_ASSISTANT) {
                if (empty($supervisor)) {
                    $errorFields['supervisor'] = "required";
                } else {
                    $supervisor = $this->directorRepository->findOneBy([
                        'id' => $supervisor,
                        'region' => $region,
                        'role' => $this->directorRepository::DIRECTOR_ROLE_AREA
                    ]);

                    if (is_null($supervisor)) {
                        $errorFields['supervisor'] = "invalid";
                    }
                }
            }

            if (empty($payType)) {
                $errorFields['payType'] = "required";
            } elseif (!in_array($payType, $availablePayTypes)) {
                $errorFields['payType'] = "invalid";
            }

            if (!empty($errorFields)) {
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

        if ($code == Response::HTTP_OK) {
            // Check for user existance
            $newUser = $this->userRepository->findOneBy([
                'email' => $email
            ]);
            $isNewUser = false;

            // If the user doesn't exist, create it
            if (is_null($newUser)) {
                $tempPasswd = Util::generatePassword();
                $isNewUser = true;
                $newUser = new User();
                $newUser->setEmail($email);
                $newUser->setFirstName($firstName);
                $newUser->setIsAdmin($isAdmin);
                $newUser->setLastName($lastName);
                $newUser->securePassword($tempPasswd);
                $this->userRepository->save($newUser);
            }

            // Check for the director existance
            if (!$isNewUser) {
                $oldDirector = $this->directorRepository->findOneBy([
                    'user' => $newUser,
                    'region' => $region,
                    'role' => $role
                ]);

                if (!is_null($oldDirector)) {
                    $code = Response::HTTP_UNPROCESSABLE_ENTITY;
                }
            }
        }

        if ($code == Response::HTTP_OK) {
            $areaPercentage = empty($areaPercentage) ? 0 : $areaPercentage / 100;
            $fixedPercentage = empty($fixedPercentage) ? 0 : $fixedPercentage / 100;
            $greenLigthPercentage = empty($greenLigthPercentage) ? 0 : $greenLigthPercentage / 100;
            $greyLigthPercentage = empty($greyLigthPercentage) ? 0 : $greyLigthPercentage / 100;
            $launchPercentage = empty($launchPercentage) ? 0 : $launchPercentage / 100;
            $redLigthPercentage = empty($redLigthPercentage) ? 0 : $redLigthPercentage / 100;
            $yellowLigthPercentage = empty($yellowLigthPercentage) ? 0 : $yellowLigthPercentage / 100;

            $director = new Director();
            $director->setAreaPercentage($areaPercentage);
            $director->setFixedPercentage($fixedPercentage);
            $director->setFreeAccount($isFreeAccount);
            $director->setGreenLightPercentage($greenLigthPercentage);
            $director->setGreyLightPercentage($greyLigthPercentage);
            $director->setLaunchPercentage($launchPercentage);
            $director->setPayType($payType);
            $director->setRedLightPercentage($redLigthPercentage);
            $director->setRegion($region);
            $director->setRole($role);
            $director->setSupervisor($supervisor);
            $director->setUser($newUser);
            $director->setYellowLightPercentage($yellowLigthPercentage);
            $this->directorRepository->save($director);

            try {
                if ($isNewUser) {
                    $send = $this->userRepository->sendNewUserEmail($newUser, $tempPasswd);
                    if (!$send) {
                        throw new Exception("email_not_sent", 500);
                    }
                }

                $send = $this->directorRepository->sendDirectorAssignmentEmail($director);
                if (!$send) {
                    throw new Exception("email_not_sent", 500);
                }
            } catch (Exception $e) {
                $code = $e->getCode() === 500 ? Response::HTTP_INTERNAL_SERVER_ERROR : Response::HTTP_BAD_REQUEST;
                return new JsonResponse($e->getMessage(), $code);
            }

            return new JsonResponse($this->directorFormatter->formatFull($director), Response::HTTP_CREATED);
        } else {
            $errorFields = $code == Response::HTTP_BAD_REQUEST ? $errorFields : null;
            return new JsonResponse($errorFields, $code);
        }
    }

    /**
     * Edit a director
     * Canges can be made from admin or directors having at last EXECUTIVE role.
     *
     * @Route(path="/director/{id}", name="edit_director", methods={"PUT"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The director"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="formData",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Parameter(
     *      name="role",
     *      in="formData",
     *      type="string",
     *      description="Can be one of 'ASSISTANT', 'AREA', 'EXECUTIVE' or 'NATIONAL'"
     * )
     * @SWG\Parameter(
     *      name="isFreeAccount",
     *      in="formData",
     *      type="boolean",
     *      description="The new director is a free account?"
     * )
     * @SWG\Parameter(
     *      name="region",
     *      in="formData",
     *      type="string",
     *      description="The region of reference. Can be changed only by admins or directors with NATIONAL role"
     * )
     * @SWG\Parameter(
     *      name="supervisor",
     *      in="formData",
     *      type="string",
     *      description="An AREA director id. It is mandatory if we are changing to an ASSISTANT director"
     * )
     * @SWG\Parameter(
     *      name="payType",
     *      in="formData",
     *      type="string",
     *      description="Can be one of 'MONTHLY' or 'ANNUAL'"
     * )
     * @SWG\Parameter(
     *      name="launchPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the director takes at chapter launches"
     * )
     * @SWG\Parameter(
     *      name="greenLightPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the director takes at green light"
     * )
     * @SWG\Parameter(
     *      name="yellowLightPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the director takes at yellow light"
     * )
     * @SWG\Parameter(
     *      name="redLightPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the director takes at red light"
     * )
     * @SWG\Parameter(
     *      name="greyLightPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the director takes at grey light"
     * )
     * @SWG\Parameter(
     *      name="areaPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the area director takes"
     * )
     * @SWG\Parameter(
     *      name="fixedPercentage",
     *      in="formData",
     *      type="string",
     *      description="The perchentage that the director takes as alternative to all the other percentages"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns a Director object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="areaPercentage", type="integer"),
     *          @SWG\Property(property="email", type="integer"),
     *          @SWG\Property(property="firstName", type="integer"),
     *          @SWG\Property(property="fixedPercentage", type="integer"),
     *          @SWG\Property(property="fullName", type="string"),
     *          @SWG\Property(property="greenLightPercentage", type="integer"),
     *          @SWG\Property(property="greyLightPercentage", type="integer"),
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(property="lastName", type="integer"),
     *          @SWG\Property(property="launchPercentage", type="integer"),
     *          @SWG\Property(property="payType", type="string"),
     *          @SWG\Property(property="redLightPercentage", type="integer"),
     *          @SWG\Property(property="role", type="string"),
     *          @SWG\Property(
     *              property="supervisor",
     *              type="object",
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="string")
     *          ),
     *          @SWG\Property(property="yellowLightPercentage", type="integer")
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if some data check errors are found.",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="field_name", type="string", description="The type of the error; possible values are 'required', 'in_use' or 'invalid'")
     *          )
     *      )
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if the user or the emulated user are not authorized to meke the requested changes."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Directors")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function editDirector(Director $director, Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $actAsId = $request->get("actAs");
        $code = Response::HTTP_OK;
        $errorFields = [];
        $region = $director->getRegion();
        $user = $this->getUser();
        $isAdmin = $user->isAdmin() && is_null($actAsId);

        $checkUser = $this->userRepository->checkUser($user, $actAsId);
        $actAs = Util::arrayGetValue($checkUser, 'user');
        $code = Util::arrayGetValue($checkUser, 'code');

        if ($code == Response::HTTP_OK && !$isAdmin) {
            $u = is_null($actAsId) ? $user : $actAs;
            $checkDirectorRole = $this->directorRepository->checkDirectorRole($u, $region);

            $code = Util::arrayGetValue($checkDirectorRole, 'code', $code);
            $director = Util::arrayGetValue($checkDirectorRole, 'director', null);
            $performerRole = $director->getRole();
        }

        if ($code == Response::HTTP_OK) {
            $performerRole = $isAdmin ? $this->directorRepository::DIRECTOR_ROLE_NATIONAL : $performerRole;
            if (!in_array($performerRole, [
                $this->directorRepository::DIRECTOR_ROLE_NATIONAL,
                $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE
            ])) {
                $code = Response::HTTP_FORBIDDEN;
            }
        }

        if ($code == Response::HTTP_OK) {
            $role = strtoupper(trim($request->get("role")));
            $isFreeAccount = $request->get("isFreeAccount");
            $newRegion = $request->get("region");
            $supervisor = $request->get("supervisor");
            $payType = strtoupper(trim($request->get("payType")));
            $launchPercentage = $request->get("launchPercentage");
            $greenLigthPercentage = $request->get("greenLightPercentage");
            $yellowLigthPercentage = $request->get("yellowLightPercentage");
            $redLigthPercentage = $request->get("redLightPercentage");
            $greyLigthPercentage = $request->get("greyLightPercentage");
            $areaPercentage = $request->get("areaPercentage");
            $fixedPercentage = $request->get("fixedPercentage");

            $availablePayTypes = [
                $this->directorRepository::DIRECTOR_PAY_TYPE_ANNUAL,
                $this->directorRepository::DIRECTOR_PAY_TYPE_MONTHLY
            ];
            $availableRoles = [
                $this->directorRepository::DIRECTOR_ROLE_AREA,
                $this->directorRepository::DIRECTOR_ROLE_ASSISTANT,
                $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE,
                $this->directorRepository::DIRECTOR_ROLE_NATIONAL
            ];
            $errorFields = $fields = [];

            if (!empty($role) && !in_array($role, $availableRoles)) {
                $errorFields['role'] = "invalid";
            } elseif (!empty($role)) {
                $fields['role'] = $role;
            }

            if (!empty($newRegion)) {
                $newRegion = $this->regionRepository->find($newRegion);
                if (is_null($newRegion)) {
                    $errorFields['region'] = "invalid";
                } else {
                    $fields['region'] = $newRegion;
                }
            }

            if (!empty($payType) && !in_array($payType, $availablePayTypes)) {
                $errorFields['payType'] = "invalid";
            } elseif (!empty($payType)) {
                $fields['payType'] = $payType;
            }

            if (!empty($launchPercentage) && !is_numeric($launchPercentage)) {
                $errorFields['launchPercentage'] = "invalid";
            } elseif (!empty($launchPercentage)) {
                $fields['launchPercentage'] = $launchPercentage;
            }

            if (!empty($greenLigthPercentage) && !is_numeric($greenLigthPercentage)) {
                $errorFields['greenLigthPercentage'] = "invalid";
            } elseif (!empty($greenLigthPercentage)) {
                $fields['greenLigthPercentage'] = $greenLigthPercentage;
            }

            if (!empty($yellowLigthPercentage) && !is_numeric($yellowLigthPercentage)) {
                $errorFields['yellowLigthPercentage'] = "invalid";
            } elseif (!empty($yellowLigthPercentage)) {
                $fields['yellowLigthPercentage'] = $yellowLigthPercentage;
            }

            if (!empty($redLigthPercentage) && !is_numeric($redLigthPercentage)) {
                $errorFields['redLigthPercentage'] = "invalid";
            } elseif (!empty($redLigthPercentage)) {
                $fields['redLigthPercentage'] = $redLigthPercentage;
            }

            if (!empty($greyLigthPercentage) && !is_numeric($greyLigthPercentage)) {
                $errorFields['greyLigthPercentage'] = "invalid";
            } elseif (!empty($greyLigthPercentage)) {
                $fields['greyLigthPercentage'] = $greyLigthPercentage;
            }

            if (!empty($areaPercentage) && !is_numeric($areaPercentage)) {
                $errorFields['areaPercentage'] = "invalid";
            } elseif (!empty($areaPercentage)) {
                $fields['areaPercentage'] = $areaPercentage;
            }

            if (!empty($fixedPercentage) && !is_numeric($fixedPercentage)) {
                $errorFields['fixedPercentage'] = "invalid";
            } elseif (!empty($fixedPercentage)) {
                $fields['fixedPercentage'] = $fixedPercentage;
            }

            switch (true) {
                case $performerRole == $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE:
                    if (!empty($role) && $role == $this->directorRepository::DIRECTOR_ROLE_NATIONAL) {
                        $errorFields['role'] = "too_high";
                    }

                    if (!empty($newRegion) && $newRegion != $region) {
                        $errorFields['region'] = "invalid";
                    }
                default:
                    if (!empty($role)) {
                        if (!!$isFreeAccount && $role != $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE) {
                            $errorFields['isFreeAccount'] = "invalid";
                        } elseif (!is_null($isFreeAccount)) {
                            $fields['isFreeAccount'] = !!$isFreeAccount;
                        }
                    }

                    if ($role == $this->directorRepository::DIRECTOR_ROLE_ASSISTANT) {
                        if (empty($supervisor)) {
                            $errorFields['supervisor'] = "required";
                        } else {
                            $supervisor = $this->directorRepository->findOneBy([
                                'id' => $supervisor,
                                'region' => $region,
                                'role' => $this->directorRepository::DIRECTOR_ROLE_AREA
                            ]);

                            if (is_null($supervisor)) {
                                $errorFields['supervisor'] = "invalid";
                            } else {
                                $fields['supervisor'] = $supervisor;
                            }
                        }
                    }
            }

            if (!empty($errorFields)) {
                $code = Response::HTTP_BAD_REQUEST;
            }
        }

        if ($code == Response::HTTP_OK) {
            if (array_key_exists('role', $fields)) {
                $director->setRole(Util::arrayGetValue('role', $fields));
            }

            if (array_key_exists('isFreeAccount', $fields)) {
                $director->setFreeAccount(Util::arrayGetValue('isFreeAccount', $fields));
            }

            if (array_key_exists('region', $fields)) {
                $director->setRegion(Util::arrayGetValue('region', $fields));
            }

            if (array_key_exists('supervisor', $fields)) {
                $director->setSupervisor(Util::arrayGetValue('supervisor', $fields));
            }

            if (array_key_exists('payType', $fields)) {
                $director->setPayType(Util::arrayGetValue('payType', $fields));
            }

            if (array_key_exists('launchPercentage', $fields)) {
                $director->setLaunchPercentage((int)Util::arrayGetValue('launchPercentage', $fields, 0) / 100);
            }

            if (array_key_exists('greenLigthPercentage', $fields)) {
                $director->setGreenLightPercentage((int)Util::arrayGetValue('greenLigthPercentage', $fields, 0) / 100);
            }

            if (array_key_exists('yellowLigthPercentage', $fields)) {
                $director->setYellowLightPercentage((int)Util::arrayGetValue('yellowLigthPercentage', $fields, 0) / 100);
            }

            if (array_key_exists('redLigthPercentage', $fields)) {
                $director->setRedLightPercentage((int)Util::arrayGetValue('redLigthPercentage', $fields, 0) / 100);
            }

            if (array_key_exists('greyLigthPercentage', $fields)) {
                $director->setGreyLightPercentage((int)Util::arrayGetValue('greyLigthPercentage', $fields, 0) / 100);
            }

            if (array_key_exists('areaPercentage', $fields)) {
                $director->setAreaPercentage((int)Util::arrayGetValue('areaPercentage', $fields, 0) / 100);
            }

            if (array_key_exists('fixedPercentage', $fields)) {
                $director->setFixedPercentage((int)Util::arrayGetValue('fixedPercentage', $fields, 0) / 100);
            }

            $this->entityManager->flush();

            return new JsonResponse($this->directorFormatter->formatFull($director));
        } else {
            $errorFields = empty($errorFields) ? null : $errorFields;
            return new JsonResponse($errorFields, $code);
        }
    }

    /**
     * Get all region directors or only area directors per region
     *
     * @Route(path="/{id}/directors", name="list_directors", methods={"GET"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The region of reference"
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
     * @SWG\Parameter(
     *      name="onlyArea",
     *      in="query",
     *      type="boolean"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns a list of Director object",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="areaPercentage", type="integer"),
     *              @SWG\Property(property="email", type="integer"),
     *              @SWG\Property(property="firstName", type="integer"),
     *              @SWG\Property(property="fixedPercentage", type="integer"),
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="greenLightPercentage", type="integer"),
     *              @SWG\Property(property="greyLightPercentage", type="integer"),
     *              @SWG\Property(property="id", type="string"),
     *              @SWG\Property(property="lastName", type="integer"),
     *              @SWG\Property(property="launchPercentage", type="integer"),
     *              @SWG\Property(property="payType", type="string"),
     *              @SWG\Property(property="redLightPercentage", type="integer"),
     *              @SWG\Property(property="role", type="string"),
     *              @SWG\Property(
     *                  property="supervisor",
     *                  type="object",
     *                  @SWG\Property(property="fullName", type="string"),
     *                  @SWG\Property(property="id", type="string")
     *              ),
     *              @SWG\Property(property="yellowLightPercentage", type="integer")
     *          )
     *      )
     * )
     * @SWG\Response(
     *      response=403,
     *      description="Returned if actAs is given but the current user is not an admin or if a valid role is given but the user has not that role for the specified region."
     * )
     * @SWG\Response(
     *      response=404,
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Directors")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function listDirectors(Region $region, Request $request): Response
    {
        $actAsId = $request->get("actAs");
        $code = Response::HTTP_OK;
        $onlyArea = !!(int)$request->get("onlyArea");
        $role = $request->get("role");
        $user = $this->getUser();
        $isAdmin = $user->isAdmin() && is_null($actAsId);

        $checkUser = $this->userRepository->checkUser($user, $actAsId);
        $actAs = Util::arrayGetValue($checkUser, 'user');
        $code = Util::arrayGetValue($checkUser, 'code');

        if ($code == Response::HTTP_OK && !$isAdmin) {
            $u = is_null($actAsId) ? $user : $actAs;
            $checkDirectorRole = $this->directorRepository->checkDirectorRole($u, $region, $role);

            $code = Util::arrayGetValue($checkDirectorRole, 'code', $code);
            $director = Util::arrayGetValue($checkDirectorRole, 'director', null);
            $role = $director ? $director->getRole() : null;
        }

        if ($code == Response::HTTP_OK) {
            $role = $isAdmin ? $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE : $role;
            if (!in_array($role, [
                $this->directorRepository::DIRECTOR_ROLE_NATIONAL,
                $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE
            ])) {
                $code = Response::HTTP_FORBIDDEN;
            }
        }

        if ($code == Response::HTTP_OK) {
            $criteria = [
                'region' => $region
            ];

            if ($onlyArea) {
                $criteria['role'] = $this->directorRepository::DIRECTOR_ROLE_AREA;
            }

            $directors = $this->directorRepository->findBy($criteria);
            usort($directors, function ($d1, $d2) {
                $fn1 = $d1->getUser()->getFullName();
                $fn2 = $d2->getUser()->getFullName();

                return $fn1 < $fn2 ? -1 : ($fn1 > $fn2 ? 1 : 0);
            });

            return new JsonResponse(array_map(function ($d) {
                return $this->directorFormatter->formatFull($d);
            }, $directors));
        } else {
            return new JsonResponse(null, $code);
        }
    }

    /**
     * Importer for users and directors from the old DB
     *
     * @Route(path="/directors/import", name="director_importer", methods={"GET","PATCH"})
     *
     * @SWG\Response(
     *      response=200,
     *      description="All users and directors has been imported correctly"
     * )
     * @SWG\Response(
     *      response=500,
     *      description="Some import error occured",
     *      @SWG\Schema(type="string", description="The error message")
     * )
     * @SWG\Tag(name="Directors")
     * @Security(name="none")
     *
     * @return Response
     */
    public function importDirectorssFromOldDB(): Response
    {
        //TODO: Rimuovere o commentare la riga qui sotto se si intende rieseguire l'importazione
        return new JsonResponse();

        //Entity managers and repositories
        /** @var EntityManagerInterface */
        $em = $this->getDoctrine()->getManager('default');

        /** @var DirectorRepository */
        $directorRepository = $this->getDoctrine()->getRepository(Director::class, 'default');

        /** @var RegionRepository */
        $regionRepository = $this->getDoctrine()->getRepository(Region::class, 'default');

        /** @var UserRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class, 'default');

        /** @var OldDirectorRepository */
        $oldDirectorRepository = $this->getDoctrine()->getRepository(OldDirector::class, 'old_db');

        /** @var OldRegionRepository */
        $oldRegionRepository = $this->getDoctrine()->getRepository(OldRegion::class, 'old_db');

        //Retrieve data from old table
        $oldDirectors = $oldDirectorRepository->findAll();

        //Create users and directors data arrays
        $users = [];

        /** @var OldDirector */
        $oldDirector = null;

        foreach ($oldDirectors as $oldDirector) {
            $email = $oldDirector->getEmail();

            if (!array_key_exists($email, $users)) {
                $fullName = explode(" ", strtolower($oldDirector->getNome()));

                foreach ($fullName as &$name) {
                    $name = ucfirst($name);
                }

                $firstName = array_shift($fullName);

                if (count($fullName) == 1) {
                    $lastName = $fullName[0];
                } elseif (count($fullName) > 1) {
                    if (!in_array($fullName[0], ["De", "Di", "Lo"])) {
                        $firstName .= " " . array_shift($fullName);
                    }

                    $lastName = implode(" ", $fullName);
                }

                $users[$email] = [
                    'directors' => [],
                    'email' => $email,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'password' => $oldDirector->getPassword()
                ];
            }

            $compArea = $oldDirector->getCompArea();
            $region = $oldDirector->getRegion($oldRegionRepository);

            //TODO: rimuovere in caso di riesecuzione e dopo la risoluzione dei problemi sotto elencati
            // Inserito perché Christian Golino ha un director associato a una regione inesistente e
            // Gabriele Pinto ha un director con un compenso per Area del 13300%
            if ($compArea > 1 || is_null($region)) {
                continue;
            }

            $users[$email]['directors'][] = [
                'area' => $oldDirector->getArea(),
                'compArea' => $oldDirector->getCompArea(),
                'compenso' => strtoupper($oldDirector->getCompenso()),
                'compFisso' => $oldDirector->getCompFisso(),
                'greenLight' => $oldDirector->getGreenLight(),
                'greyLight' => $oldDirector->getGreyLight(),
                'id' => $oldDirector->getId(),
                'lancio' => $oldDirector->getLancio(),
                'level' => $oldDirector->getLevel(), //0-assistant, 1-executive, 2-area, 3-national
                'redLight' => $oldDirector->getRedLight(),
                'region' => $region,
                'yellowLight' => $oldDirector->getYellowLight()
            ];
        }

        //Truncate the User and Director tables
        $userClassMetaData = $em->getClassMetadata(User::class);
        $directorClassMetaData = $em->getClassMetadata(Director::class);
        $connection = $em->getConnection();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->query('DELETE FROM ' . $userClassMetaData->getTableName());
            $connection->query('DELETE FROM ' . $directorClassMetaData->getTableName());
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (Exception $ex) {
            $connection->rollBack();
            die("Oooops!");
        }

        //Fill the new User and Director table
        try {
            $assistants = $areas = $regions = [];

            foreach ($users as $oldUser) {
                $user = new User();
                $user->setEmail($oldUser['email']);
                $user->setFirstName($oldUser['firstName']);
                $user->setIsAdmin(false);
                $user->setLastName($oldUser['lastName']);
                $user->securePassword($oldUser['password']);
                $userRepository->save($user);

                foreach ($oldUser['directors'] as $oldDirector) {
                    //PayType
                    $payType = $oldDirector['compenso'] == 'MENSILE' ? $directorRepository::DIRECTOR_PAY_TYPE_MONTHLY : $directorRepository::DIRECTOR_PAY_TYPE_ANNUAL;

                    //Region
                    $regionName = $oldDirector['region']->getNome();
                    $regionIndex = md5($regionName);
                    if (!array_key_exists($regionIndex, $regions)) {
                        $regions[$regionIndex] = $regionRepository->findOneBy([
                            'name' => $regionName
                        ]);
                    }
                    $region = $regions[$regionIndex];

                    //Role
                    switch ($oldDirector['level']) {
                        case 0:
                            $role = $directorRepository::DIRECTOR_ROLE_ASSISTANT;
                        break;
                        case 1:
                            $role = $directorRepository::DIRECTOR_ROLE_EXECUTIVE;
                        break;
                        case 2:
                            $role = $directorRepository::DIRECTOR_ROLE_AREA;
                        break;
                        case 3:
                            $role = $directorRepository::DIRECTOR_ROLE_NATIONAL;
                        break;
                    }

                    $director = new Director();
                    $director->setAreaPercentage($oldDirector['compArea']);
                    $director->setFixedPercentage($oldDirector['compFisso']);
                    $director->setFreeAccount(false);
                    $director->setGreenLightPercentage($oldDirector['greenLight']);
                    $director->setGreyLightPercentage($oldDirector['greyLight']);
                    $director->setLaunchPercentage($oldDirector['lancio']);
                    $director->setPayType($payType);
                    $director->setRedLightPercentage($oldDirector['redLight']);
                    $director->setRegion($region);
                    $director->setRole($role);
                    $director->setUser($user);
                    $director->setYellowLightPercentage($oldDirector['yellowLight']);
                    $directorRepository->save($director);

                    //Supervisors assignments
                    if ($oldDirector['level'] == 0 && !in_array($oldDirector['area'], [0, $oldDirector['id']])) {
                        $assistants[$director->getId()] = $oldDirector['area'];
                    }

                    if ($oldDirector['level'] == 2) {
                        $areas[$oldDirector['id']] = $director;
                    }
                }
            }

            foreach ($assistants as $assistantId => $areaIndex) {
                $area = Util::arrayGetValue($areas, $areaIndex, null);
                if (!is_null($area)) {
                    $assistant = $directorRepository->find($assistantId);
                    $assistant->setSupervisor($area);
                    $em->flush();
                }
            }
        } catch (Exception $ex) {
            return new JsonResponse($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse();
    }
}
