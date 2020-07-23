<?php

namespace App\Controller;

use Exception;
use App\Util\Util;
use App\Entity\User;
use App\Entity\Region;
use App\Util\Constants;
use App\Util\Validator;
use Swagger\Annotations as SWG;
use App\Formatter\UserFormatter;
use App\Repository\UserRepository;
use App\Repository\DirectorRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/api")
**/
class UserController extends AbstractController
{
    /** @var DirectorRepository */
    private $directorRepository;

    /** @var UserFormatter */
    private $userFormatter;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(
        DirectorRepository $directorRepository,
        UserFormatter $userFormatter,
        UserRepository $userRepository
    ) {
        $this->directorRepository = $directorRepository;
        $this->userFormatter = $userFormatter;
        $this->userRepository = $userRepository;
    }

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
    public function deleteUser(User $user, Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $roleCheck = [
            Constants::ROLE_NATIONAL
        ];
        $performerData = Util::getPerformerData($this->getUser(), null, $roleCheck, $this->userRepository, $this->directorRepository, $request->get("actAs"), Constants::ROLE_NATIONAL);

        // Assign $actAs, $code, $director, $isAdmin and $role
        foreach ($performerData as $var => $value) {
            $$var = $value;
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
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="field_name", type="string", description="The type of the error; possible values are 'required' or 'invalid'")
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
     * @Route("/{id}/users", name="users_list_per_region", methods={"GET"})
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


    /**
     * Change password
     *
     * @Route("/changePassword", name="change_password", methods={"PUT"})
     *
     */
    public function changePassword(Request $request): Response
    {
        $request = Util::normalizeRequest($request);
        $pwd1 = $request->get("pwd1");
        $pwd2 = $request->get("pwd2");

        if($pwd1 !== $pwd2) {
            return new JsonResponse("Passwords not matching", Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        $user->securePassword($pwd1);
        try {
            $this->userRepository->save($user);
        } catch (Exception $e) {
            header("eccezione: " . $e->getMessage());
        }
        return new JsonResponse(true, Response::HTTP_OK);
    }

   


    /**
     * Get a list of users for autocomplete
     *
     * @Route(path="/user/search", name="serch_users", methods={"GET"})
     *
     * @SWG\Parameter(
     *      name="term",
     *      in="formData",
     *      type="string",
     *      description="The term of search"
     * )
     * @SWG\Response(
     *      response=200,
     *      description="Alist of users filtered by term",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="firstName", type="string", description="The user's first name"),
     *              @SWG\Property(property="lastName", type="string", description="The user's last name"),
     *              @SWG\Property(property="label", type="string", description="In the form 'Name Surname (email)'"),
     *              @SWG\Property(property="value", type="string", description="The user's email")
     *          )
     *      )
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function searchUsers(Request $request): Response
    {
        $request = Util::normalizeRequest($request);

        $term = trim($request->get("term"));

        if (strlen($term) < 3) {
            return new JsonResponse([]);
        }

        $users = $this->userRepository->searchUsers($term);
        usort($users, function ($u1, $u2) {
            $fn1 = $u1->getFullName();
            $fn2 = $u2->getFullName();

            return $fn1 < $fn2 ? -1 : ($fn1 > $fn2 ? 1 : 0);
        });

        return new JsonResponse(array_map(function ($user) {
            return $this->userFormatter->formatForAutocomplete($user);
        }, $users));
    }
}
