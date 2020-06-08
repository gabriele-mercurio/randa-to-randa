<?php

namespace App\Controller;

use App\Entity\Director;
use App\Entity\Region;
use App\Entity\User;
use App\Formatter\DirectorFormatter;
use App\Repository\DirectorRepository;
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

    /** @var UserRepository */
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        DirectorFormatter $directorFormatter,
        DirectorRepository $directorRepository,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->directorFormatter = $directorFormatter;
        $this->directorRepository = $directorRepository;
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
     *      name="region",
     *      in="formData",
     *      type="string",
     *      description="The region of reference. Can be null only if we are creating a NATIONAL director"
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
     *          @SWG\Property(property="fixedPercentage", type="float"),
     *          @SWG\Property(property="fullName", type="string"),
     *          @SWG\Property(property="greenLightPercentage", type="float"),
     *          @SWG\Property(property="greyLightPercentage", type="float"),
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(property="launchPercentage", type="float"),
     *          @SWG\Property(property="payType", type="string"),
     *          @SWG\Property(property="redLightPercentage", type="float"),
     *          @SWG\Property(property="role", type="string"),
     *          @SWG\Property(
     *              property="supervisor",
     *              type="object",
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="string")
     *          ),
     *          @SWG\Property(property="yellowLightPercentage", type="float")
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if one or more required fields are empty or if one or more fields are invalid.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="fields",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(property="field_name", type="string", description="The type of the error; possible values are 'required', 'in_use' or 'invalid'")
     *              )
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

        $checkUser = $this->userRepository->checkUser($user, $actAsId);
        $actAs = Util::arrayGetValue($checkUser, 'user');
        $code = Util::arrayGetValue($checkUser, 'code');

        if ($code == Response::HTTP_OK) {
            if ($user->isAdmin() && is_null($actAsId)) {
                $performerRole = $this->directorRepository::DIRECTOR_ROLE_NATIONAL;
            } else {
                $u = is_null($actAsId) ? $user : $actAs;
                $director = $this->directorRepository->findOneBy([
                    'user' => $u,
                    'region' => $region,
                    'role' => [
                        $this->directorRepository::DIRECTOR_ROLE_NATIONAL,
                        $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE
                    ]
                ]);

                if (is_null($director)) {
                    $code = Response::HTTP_FORBIDDEN;
                } else {
                    $performerRole = $director->getRole();
                }
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
            $fixedPercentage = empty($fixedPercentage) ? 0 : $fixedPercentage / 100;
            $greenLigthPercentage = empty($greenLigthPercentage) ? 0 : $greenLigthPercentage / 100;
            $greyLigthPercentage = empty($greyLigthPercentage) ? 0 : $greyLigthPercentage / 100;
            $launchPercentage = empty($launchPercentage) ? 0 : $launchPercentage / 100;
            $redLigthPercentage = empty($redLigthPercentage) ? 0 : $redLigthPercentage / 100;
            $yellowLigthPercentage = empty($yellowLigthPercentage) ? 0 : $yellowLigthPercentage / 100;

            $director = new Director();
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
     * Edit a chapter
     * Canges to members and prevResumeDate can be made from any authorized directors; for all other fields the user must be admin or have EXECUTIVE role.
     * Changes to launch date fields from prev to actual are not allowed: there are specific API calls to launch a coregroup or a chapter, use them instead
     *
     * @Route(path="/chapter/{id}", name="edit_chapter", methods={"PUT"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The chapter"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="formData",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Parameter(
     *      name="name",
     *      in="formData",
     *      type="string",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="director",
     *      in="formData",
     *      type="string",
     *      description="The user id of the designated chapter director",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="members",
     *      in="formData",
     *      type="string",
     *      description="Optional previsioning coregroup launch date."
     * )
     * @SWG\Parameter(
     *      name="prevLaunchCoregroupDate",
     *      in="formData",
     *      type="string",
     *      description="Optional actual coregroup launch date. If this date is given prevLaunchCoregroupDate is not given."
     * )
     * @SWG\Parameter(
     *      name="actualLaunchCoregroupDate",
     *      in="formData",
     *      type="string",
     *      description="Optional actual coregroup launch date. If this date is given prevLaunchCoregroupDate is not given."
     * )
     * @SWG\Parameter(
     *      name="prevLaunchChapterDate",
     *      in="formData",
     *      type="string",
     *      description="Optional previsioning chapter launch date."
     * )
     * @SWG\Parameter(
     *      name="actualLaunchChapterDate",
     *      in="formData",
     *      type="string",
     *      description="Optional actual chapter launch date. If this date is given prevLaunchChapterDate is not given."
     * )
     * @SWG\Parameter(
     *      name="prevResumeDate",
     *      in="formData",
     *      type="string",
     *      description="Optional previsioning chapter resume date."
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns a Chapter object",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="chapterLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="closureDate", type="string", description="Closure date"),
     *          @SWG\Property(
     *              property="coreGroupLaunch",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="currentState", type="string", description="Available values: PROJECT, CORE_GROUP or CHAPTER"),
     *          @SWG\Property(
     *              property="director",
     *              type="object",
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="integer")
     *          ),
     *          @SWG\Property(property="id", type="string"),
     *          @SWG\Property(property="members", type="integer"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(
     *              property="resume",
     *              type="object",
     *              @SWG\Property(property="actual", type="string", description="Actual date"),
     *              @SWG\Property(property="prev", type="string", description="Expected date")
     *          ),
     *          @SWG\Property(property="suspDate", type="string", description="Suspension date"),
     *          @SWG\Property(property="warning", type="string", description="Available values: NULL, 'CORE_GROUP' or 'CHAPTER'")
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if some data check errors are found.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="fields",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(property="field_name", type="string", description="The type of the error; possible values are 'required', 'in_use' or 'invalid'")
     *              )
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
     * @SWG\Response(
     *      response=409,
     *      description="Returned when are given dates that change the chapter status. Use dedicated APIs instead."
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    // public function editChapter(Chapter $chapter, Request $request): Response
    // {
    //     $request = Util::normalizeRequest($request);

    //     $actAs = $request->get("actAs");
    //     $code = Response::HTTP_OK;
    //     $errorFields = $fields = [];
    //     $user = $this->getUser();

    //     $checkUser = $this->userRepository->checkUser($user, $actAs);
    //     $user = Util::arrayGetValue($checkUser, 'user');
    //     $code = Util::arrayGetValue($checkUser, 'code');

    //     $region = $chapter->getRegion();

    //     if ($code == Response::HTTP_OK) {
    //         if ($user->isAdmin()) {
    //             $role = $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE;
    //         } else {
    //             $checkDirectorRole = $this->directorRepository->checkDirectorRole($user, $region);
    //             $code = Util::arrayGetValue($checkDirectorRole, 'code', $code);
    //             $director = Util::arrayGetValue($checkDirectorRole, 'director', null);

    //             if (!is_null($director)) {
    //                 $role = $director->getRole();
    //             }
    //         }
    //     }

    //     if ($code == Response::HTTP_OK) {
    //         $name = $request->get("name");
    //         $chapterDirector = $request->get("director");
    //         $members = $request->get("members");
    //         $prevLaunchCoregroupDate = $request->get("prevLaunchCoregroupDate");
    //         $actualLaunchCoregroupDate = $request->get("actualLaunchCoregroupDate");
    //         $prevLaunchChapterDate = $request->get("prevLaunchChapterDate");
    //         $actualLaunchChapterDate = $request->get("actualLaunchChapterDate");
    //         $prevResumeDate = $request->get("prevResumeDate");

    //         $today = Util::UTCDateTime();

    //         // Check Name
    //         if (!empty($name)) {
    //             $name = trim($name);
    //             if (empty($name)) {
    //                 $errorFields['name'] = "required";
    //             } elseif ($this->chapterRepository->existsOtherWithSameFields($chapter, [
    //                 'name' => $name,
    //                 'region' => $region
    //             ])) {
    //                 $errorFields['name'] = "in_use";
    //             } else {
    //                 $fields['name'] = $name;
    //             }
    //         }

    //         // Check Chapter Director
    //         if (!empty($chapterDirector)) {
    //             $chapterDirector = $this->userRepository->find(trim($chapterDirector));
    //             if (is_null($chapterDirector)) {
    //                 $errorFields['director'] = "invalid";
    //             } else {
    //                 $fields['director'] = $chapterDirector;
    //             }
    //         }

    //         // Check Members
    //         if (!empty($members)) {
    //             $members = (int) $members;
    //             if (is_nan($members) || $members < 0) {
    //                 $errorFields['members'] = "invalid";
    //             } else {
    //                 $fields['members'] = $members;
    //             }
    //         }

    //         // Check Coregroup and Chapter dates
    //         switch ($chapter->getCurrentState()) {
    //             case $this->chapterRepository::CHAPTER_CURRENT_STATE_PROJECT:
    //                 if (!empty($prevLaunchCoregroupDate)) {
    //                     $prevLaunchCoregroupDate = trim($prevLaunchCoregroupDate);
    //                     if (!empty($prevLaunchCoregroupDate)) {
    //                         try {
    //                             $prevLaunchCoregroupDate = Util::UTCDateTime($prevLaunchCoregroupDate);
    //                         } catch (Exception $ex) {
    //                             $errorFields['prevLaunchCoregroupDate'] = "invalid";
    //                         }

    //                         if (!array_key_exists('prevLaunchCoregroupDate', $errorFields)) {
    //                             if ($prevLaunchCoregroupDate < $today) {
    //                                 $errorFields['prevLaunchCoregroupDate'] = "invalid";
    //                             } else {
    //                                 $fields['prevLaunchCoregroupDate'] = $prevLaunchCoregroupDate;
    //                             }
    //                         }
    //                     }
    //                 }

    //                 if (!empty($actualLaunchCoregroupDate)) {
    //                     $errorFields['actualLaunchCoregroupDate'] = "conflict";
    //                 }

    //                 if (!empty($prevLaunchChapterDate)) {
    //                     $prevLaunchChapterDate = trim($prevLaunchChapterDate);
    //                     if (!empty($prevLaunchChapterDate)) {
    //                         try {
    //                             $prevLaunchChapterDate = Util::UTCDateTime($prevLaunchChapterDate);
    //                         } catch (Exception $ex) {
    //                             $errorFields['prevLaunchChapterDate'] = "invalid";
    //                         }

    //                         if (!array_key_exists('prevLaunchChapterDate', $errorFields)) {
    //                             if ($prevLaunchChapterDate <= $prevLaunchCoregroupDate) {
    //                                 $errorFields['prevLaunchChapterDate'] = "invalid";
    //                             } else {
    //                                 $fields['prevLaunchChapterDate'] = $prevLaunchChapterDate;
    //                             }
    //                         }
    //                     }
    //                 }

    //                 if (!empty($actualLaunchChapterDate)) {
    //                     $errorFields['actualLaunchChapterDate'] = "conflict";
    //                 }
    //             break;
    //             case $this->chapterRepository::CHAPTER_CURRENT_STATE_CORE_GROUP:
    //                 if (!empty($prevLaunchCoregroupDate)) {
    //                     $errorFields['prevLaunchCoregroupDate'] = "conflict";
    //                 }

    //                 if (!empty($actualLaunchCoregroupDate)) {
    //                     $errorFields['actualLaunchCoregroupDate'] = "conflict";
    //                 }

    //                 if (!empty($prevLaunchChapterDate)) {
    //                     $prevLaunchChapterDate = trim($prevLaunchChapterDate);
    //                     if (!empty($prevLaunchChapterDate)) {
    //                         try {
    //                             $actualLaunchChapterDate = Util::UTCDateTime($actualLaunchChapterDate);
    //                         } catch (Exception $ex) {
    //                             $errorFields['prevLaunchChapterDate'] = "invalid";
    //                         }

    //                         if (!array_key_exists('prevLaunchChapterDate', $errorFields)) {
    //                             if ($prevLaunchChapterDate <= $today) {
    //                                 $errorFields['prevLaunchChapterDate'] = "invalid";
    //                             } else {
    //                                 $fields['prevLaunchChapterDate'] = $prevLaunchChapterDate;
    //                             }
    //                         }
    //                     }
    //                 }

    //                 if (!empty($actualLaunchChapterDate)) {
    //                     $errorFields['actualLaunchChapterDate'] = "conflict";
    //                 }
    //             break;
    //             default:
    //                 if (!empty($prevLaunchCoregroupDate)) {
    //                     $errorFields['prevLaunchCoregroupDate'] = "conflict";
    //                 }

    //                 if (!empty($actualLaunchCoregroupDate)) {
    //                     $errorFields['actualLaunchCoregroupDate'] = "conflict";
    //                 }

    //                 if (!empty($prevLaunchChapterDate)) {
    //                     $errorFields['prevLaunchChapterDate'] = "conflict";
    //                 }

    //                 if (!empty($actualLaunchChapterDate)) {
    //                     $errorFields['actualLaunchChapterDate'] = "conflict";
    //                 }
    //             break;
    //         }

    //         // Check Resume date
    //         if (!empty($prevResumeDate)) {
    //             $prevResumeDate = trim($prevResumeDate);
    //             if (!empty($prevResumeDate)) {
    //                 if ($chapter->getCurrentState() != $this->chapterRepository::CHAPTER_CURRENT_STATE_SUSPENDED) {
    //                     $errorFields['prevResumeDate'] = "conflict";
    //                 } else {
    //                     try {
    //                         $prevResumeDate = Util::UTCDateTime($prevResumeDate);
    //                     } catch (Exception $ex) {
    //                         $errorFields['prevResumeDate'] = "invalid";
    //                     }

    //                     if (!array_key_exists('prevResumeDate', $errorFields)) {
    //                         if ($prevResumeDate < $today) {
    //                             $errorFields['prevResumeDate'] = "conflict";
    //                         } else {
    //                             $fields['prevResumeDate'] = $prevResumeDate;
    //                         }
    //                     }
    //                 }
    //             }
    //         }

    //         if (!empty($errorFields)) {
    //             if (in_array("conflict", $errorFields)) {
    //                 $code = Response::HTTP_CONFLICT;
    //             } else {
    //                 $code = Response::HTTP_BAD_REQUEST;
    //             }
    //         }
    //     }

    //     if ($code == Response::HTTP_OK) {
    //         // Check allowed actions for user role
    //         if (in_array($role, [
    //             $this->directorRepository::DIRECTOR_ROLE_AREA,
    //             $this->directorRepository::DIRECTOR_ROLE_ASSISTANT
    //         ])) {
    //             $changingFields = array_keys($fields);
    //             $allowedFields = [
    //                 'members',
    //                 'prevLaunchCoregroupDate',
    //                 'prevLaunchChapterDate',
    //                 'prevResumeDate'
    //             ];
    //             if (count(array_diff($changingFields, $allowedFields))) {
    //                 $code = Response::HTTP_FORBIDDEN;
    //             }
    //         } elseif ($role != $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE) {
    //             $code = Response::HTTP_FORBIDDEN;
    //         }
    //     }

    //     if ($code == Response::HTTP_OK) {
    //         if (array_key_exists('name', $fields)) {
    //             $chapter->setName(Util::arrayGetValue('name', $fields));
    //         }

    //         if (array_key_exists('director', $fields)) {
    //             $chapterDirector = Util::arrayGetValue('director', $fields);
    //             $d = $this->directorRepository->findOneBy([
    //                 'user' => $chapterDirector,
    //                 'region' => $region,
    //                 'role' => $this->directorRepository::DIRECTOR_ROLE_ASSISTANT
    //             ]);

    //             if (is_null($d)) {
    //                 $d = new Director();
    //                 $d->setRegion($region);
    //                 $d->setRole($this->directorRepository::DIRECTOR_ROLE_ASSISTANT);
    //                 $d->setUser($chapterDirector);
    //                 $this->directorRepository->save($d);
    //             }

    //             $chapter->setDirector($d);
    //         }

    //         if (array_key_exists('members', $fields)) {
    //             $chapter->setMembers(Util::arrayGetValue('members', $fields));
    //         }

    //         if (array_key_exists('prevLaunchCoregroupDate', $fields)) {
    //             $chapter->setPrevLaunchCoregroupDate(Util::arrayGetValue('prevLaunchCoregroupDate', $fields));
    //         }

    //         if (array_key_exists('actualLaunchCoregroupDate', $fields)) {
    //             $chapter->setActualLaunchCoregroupDate(Util::arrayGetValue('actualLaunchCoregroupDate', $fields));
    //         }

    //         if (array_key_exists('prevLaunchChapterDate', $fields)) {
    //             $chapter->setPrevLaunchChapterDate(Util::arrayGetValue('prevLaunchChapterDate', $fields));
    //         }

    //         if (array_key_exists('actualLaunchChapterDate', $fields)) {
    //             $chapter->setActualLaunchChapterDate(Util::arrayGetValue('actualLaunchChapterDate', $fields));
    //         }

    //         $this->entityManager->flush();

    //         return new JsonResponse($this->chapterFormatter->formatFull($chapter), Response::HTTP_CREATED);
    //     } else {
    //         $errorFields = empty($errorFields) ? null : $errorFields;
    //         return new JsonResponse($errorFields, $code);
    //     }
    // }

    /**
     * Get chapters
     *
     * @Route(path="/{id}/chapters", name="chapters_list", methods={"GET"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The region"
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
     *      description="Returns an array of Chapter objects",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
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
     *      description="Returned if actAs is given but is not a valid user id."
     * )
     * @SWG\Tag(name="Chapters")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    // public function getChapters(Region $region, Request $request): Response
    // {
    //     $actAs = $request->get("actAs");
    //     $code = Response::HTTP_OK;
    //     $role = $request->get("role");
    //     $user = $this->getUser();

    //     $checkUser = $this->userRepository->checkUser($user, $actAs);
    //     $user = Util::arrayGetValue($checkUser, 'user');
    //     $code = Util::arrayGetValue($checkUser, 'code');

    //     if ($code == Response::HTTP_OK && !is_null($role) && !in_array($role, [
    //         $this->directorRepository::DIRECTOR_ROLE_AREA,
    //         $this->directorRepository::DIRECTOR_ROLE_ASSISTANT,
    //         $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE
    //     ])) {
    //         $code = Response::HTTP_BAD_REQUEST;
    //     }

    //     if ($code == Response::HTTP_OK) {
    //         $checkDirectorRole = $this->directorRepository->checkDirectorRole($user, $region, $role);

    //         $code = Util::arrayGetValue($checkDirectorRole, 'code', $code);
    //         $director = Util::arrayGetValue($checkDirectorRole, 'director', null);
    //     }

    //     if ($code == Response::HTTP_OK) {
    //         $role = $user->isAdmin() && is_null($role) ? $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE : $director->getRole();

    //         switch ($role) {
    //             case $this->directorRepository::DIRECTOR_ROLE_EXECUTIVE:
    //                 $chapters = $this->chapterRepository->findBy([
    //                     'region' => $region
    //                 ]);
    //                 break;
    //             case $this->directorRepository::DIRECTOR_ROLE_AREA:
    //                 $directors = [
    //                     $director->getId() => $director
    //                 ];
    //                 foreach ($this->directorRepository->findBy([
    //                     'supervisor' => $director
    //                 ]) as $d) {
    //                     $id = $d->getId();
    //                     if (!array_key_exists($id, $directors)) {
    //                         $directors[$id] = $d;
    //                     }
    //                 }
    //                 $directors = array_values($directors);
    //                 $chapters = [];

    //                 foreach ($directors as $d) {
    //                     foreach ($this->chapterRepository->findBy([
    //                         'director' => $d,
    //                         'region' => $region
    //                     ]) as $c) {
    //                         $id = $c->getId();
    //                         if (!array_key_exists($id, $chapters)) {
    //                             $chapters[$id] = $c;
    //                         }
    //                     }
    //                 }
    //                 $chapters = array_values($chapters);
    //                 break;
    //             case $this->directorRepository::DIRECTOR_ROLE_ASSISTANT:
    //                 $chapters = $this->chapterRepository->findBy([
    //                     'director' => $director,
    //                     'region' => $region
    //                 ]);
    //                 break;
    //         }
    //         usort($chapters, function ($c1, $c2) {
    //             $name1 = $c1->getName();
    //             $name2 = $c2->getName();
    //             return $name1 < $name2 ? -1 : ($name1 > $name2 ? 1 : 0);
    //         });

    //         return new JsonResponse(array_map(function ($c) {
    //             $today = new DateTime();
    //             $warning = null;

    //             if (is_null($c->getActualLaunchCoregroupDate()) && $c->getPrevLaunchCoregroupDate() <= $today) {
    //                 $warning = "CORE_GROUP";
    //             } elseif (is_null($c->getActualLaunchChapterDate()) && $c->getPrevLaunchChapterDate() <= $today) {
    //                 $warning = "CHAPTER";
    //             }

    //             $ret = $this->chapterFormatter->formatBase($c);
    //             $ret['warning'] = $warning;
    //             return $ret;
    //         }, $chapters));
    //     } else {
    //         return new JsonResponse(null, $code);
    //     }
    // }
}
