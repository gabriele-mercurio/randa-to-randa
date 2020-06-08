<?php

namespace App\Controller;

use App\Entity\Region;
use App\Entity\User;
use App\Formatter\UserFormatter;
use App\Repository\DirectorRepository;
use App\Repository\UserRepository;
use App\Util\Util;
use App\Util\Validator;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /** @var DirectorRepository */
    private $directorRepository;

    /** @var UserRepository */
    private $userRepository;

    /** @var UserFormatter */
    private $userFormatter;

    public function __construct(
        DirectorRepository $directorRepository,
        UserRepository $userRepository,
        UserFormatter $userFormatter
    ) {
        $this->directorRepository = $directorRepository;
        $this->userRepository = $userRepository;
        $this->userFormatter = $userFormatter;
    }

    /**
     * Create a user
     *
     * @Route("/user", name="user_create", methods={"POST"})
     *
     * @SWG\Parameter(
     *      name="email",
     *      in="formData",
     *      type="string",
     *      description="The User's email",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="firstName",
     *      in="formData",
     *      type="string",
     *      description="The User's first name",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="lastName",
     *      in="formData",
     *      type="string",
     *      description="The User's last name",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="password",
     *      in="formData",
     *      type="string",
     *      description="The User's password",
     *      required=true
     * )
     * @SWG\Response(
     *      response=201,
     *      description="Returns the created user",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="email", type="string"),
     *          @SWG\Property(property="fullName", type="string"),
     *          @SWG\Property(property="id", type="string")
     *      )
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     */
    // public function createUser(Request $request): Response
    // {
    //     $email = trim($request->request->get("email"));
    //     $firstName = trim($request->request->get("firstName"));
    //     $lastName = trim($request->request->get("lastName"));
    //     $password = $request->request->get("password");

    //     $errors = [];

    //     if (!Validator::validateEmail($email)) {
    //         $errors['email'] = "L'email è in un formato non valido";
    //     } elseif (null !== $this->userRepository->getUserByEmail($email)) {
    //         $errors['email'] = 'Email già esistente';
    //     }

    //     if (!Validator::validatePassword($password)) {
    //         $errors['password'] = "La password deve essere di almeno 6 caratteri e contenere almeno una lettera maiuscola, una minuscola ed un numero";
    //     }

    //     if (empty($errors)) {
    //         $user = new User();
    //         $user->setEmail($email);
    //         $user->setFirstName($firstName);
    //         $user->setLastName($lastName);
    //         $user->securePassword($password);

    //         $this->eventDispatcher->dispatch(new UserEvent($user), UserEvent::BEFORE_CREATE);
    //         $this->userRepository->save($user);
    //         $this->eventDispatcher->dispatch(new UserEvent($user), UserEvent::CREATED);

    //         return new JsonResponse($this->userFormatter->formatBasic($user));
    //     } else {
    //         return new JsonResponse([
    //             "errors" => $errors
    //         ], Response::HTTP_BAD_REQUEST);
    //     }
    // }

    /**
     * Delete a User
     *
     * @Route(path="/user/{id}", name="delete_user", methods={"DELETE"})
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The User's id"
     * )
     * @SWG\Parameter(
     *      name="actAs",
     *      in="formData",
     *      type="string",
     *      description="Optional parameter representing the emulated user id"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns null"
     * )
     * @SWG\Response(
     *      response=409,
     *      description="Returned when the user still has associations with the directors."
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function deleteBrand(User $user, Request $request): Response
    {
        /** @var User */
        $performer = $this->getUser();

        $request = Util::normalizeRequest($request);

        $actAsId = $request->get("actAs");
        $code = Response::HTTP_OK;

        $checkUser = $this->userRepository->checkUser($performer, $actAsId);
        $actAs = Util::arrayGetValue($checkUser, 'user');
        $code = Util::arrayGetValue($checkUser, 'code');

        if ($code == Response::HTTP_OK) {
            if (!$performer->isAdmin() || !is_null($actAsId)) {
                $u = is_null($actAsId) ? $performer : $actAs;
                $director = $this->directorRepository->findOneBy([
                    'user' => $u,
                    'role' => $this->directorRepository::DIRECTOR_ROLE_NATIONAL
                ]);

                if (is_null($director)) {
                    $code = Response::HTTP_FORBIDDEN;
                }
            }
        }

        if ($code == Response::HTTP_OK) {
            if ($this->userRepository->isInUse($user)) {
                $code = Response::HTTP_CONFLICT;
            }
        }

        if ($code == Response::HTTP_OK) {
            $this->brandRepository->delete($user);
        }

        return new JsonResponse(null, $code);
    }

    /**
     * Edit a user
     *
     * @Route("/user", name="edit_user", methods={"PUT"})
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      type="string",
     *      description="The id of the user to edit"
     * )
     * @SWG\Parameter(
     *      name="firstName",
     *      in="formData",
     *      type="string",
     *      description="The user's name"
     * )
     * @SWG\Parameter(
     *      name="lastName",
     *      in="formData",
     *      type="string",
     *      description="The user's surname"
     * )
     * @SWG\Parameter(
     *      name="oldPassword",
     *      in="formData",
     *      type="string",
     *      description="The user's current password"
     * )
     * @SWG\Parameter(
     *      name="newPassword",
     *      in="formData",
     *      type="string",
     *      description="The user's new password"
     * )
     * @SWG\Parameter(
     *      name="confirmPassword",
     *      in="formData",
     *      type="string",
     *      description="The user's new password confirmation"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Returns the updated user",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="email", type="string"),
     *          @SWG\Property(property="fullName", type="string"),
     *          @SWG\Property(property="id", type="string")
     *      )
     * )
     * @SWG\Response(
     *      response=400,
     *      description="Returned if one or more fields are invalid.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="fields",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(property="field_name", type="string", description="The type of the error; possible values are 'required' or 'invalid'")
     *              )
     *          )
     *      )
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function editUser(Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        /** @var User */
        $user = $this->getUser();

        $firstName = trim($request->get("firstName"));
        $lastName = trim($request->get("lastName"));
        $oldPasswd = trim($request->get("oldPassword"));
        $newPasswd = trim($request->get("newPassword"));
        $confirmPasswd = trim($request->get("confirmPassword"));
        $errorFields = [];
        $changePasswd = false;

        if (!empty($oldPasswd) || !empty($newPasswd) || !empty($confirmPasswd)) {
            $changePasswd = true;

            if (empty($oldPasswd)) {
                $errorFields['oldPassword'] = "required";
            } elseif (!$this->userRepository->passwordVerify($user, $oldPasswd)) {
                $errorFields['oldPassword'] = "invalid";
            }

            if (empty($newPasswd)) {
                $errorFields['newPassword'] = "required";
            } elseif (!Validator::validatePassword($newPasswd)) {
                $errorFields['newPassword'] = "invalid";
            }

            if (empty($confirmPasswd)) {
                $errorFields['confirmPassword'] = "required";
            } elseif ($confirmPasswd !== $newPasswd) {
                $errorFields['confirmPassword'] = "invalid";
            }
        }

        if (empty($errorFields)) {
            if ($changePasswd) {
                $user->securePassword($newPasswd);
            }

            if (!empty($firstName)) {
                $user->setFirstName($firstName);
            }

            if (!empty($lastName)) {
                $user->setLastName($lastName);
            }

            $this->entityManager->flush();

            return new JsonResponse($this->userFormatter->formatBasic($user));
        } else {
            return new JsonResponse($errorFields, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get all users
     *
     * @Route("/users", name="users_list", methods={"GET"})
     *
     * @SWG\Response(
     *      response=200,
     *      description="Returns all users",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="email", type="string"),
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="string")
     *          )
     *      )
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function listUsers(): Response
    {
        $users = $this->userRepository->getUsers();
        return new JsonResponse(array_map(function ($u) {
            return $this->userFormatter->formatBasic($u);
        }, $users));
    }

    /**
     * Get all users for a specific region
     *
     * @Route("{id}/users", name="users_list_per_region", methods={"GET"})
     *
     * @SWG\Response(
     *      response=200,
     *      description="Returns all users for the specified region",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="fullName", type="string"),
     *              @SWG\Property(property="id", type="string")
     *          )
     *      )
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function listUsersPerRegion(Region $region): Response
    {
        $users = $this->userRepository->getUsersPerRegion($region);
        usort($users, function ($u1, $u2) {
            $fullName1 = $u1->getFullName();
            $fullName2 = $u2->getFullName();

            return $fullName1 < $fullName2 ? -1 : ($fullName1 > $fullName2 ? 1 : 0);
        });

        return new JsonResponse(array_map(function ($user) {
            return $this->userFormatter->formatForSelectFields($user);
        }, $users));
    }
}
