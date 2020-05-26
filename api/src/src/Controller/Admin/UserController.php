<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Events\UserEvent;
use App\Formatter\UserFormatter;
use App\Repository\UserRepository;
use App\Util\Validator;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_users_")
 */
class UserController extends AbstractController
{
    /** @var UserRepository */
    private $userRepository;

    /** @var UserFormatter */
    private $userFormatter;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        UserRepository $userRepository,
        UserFormatter $userFormatter,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userRepository = $userRepository;
        $this->userFormatter = $userFormatter;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Get all users
     *
     * @Route("/users", name="list", methods={"GET"})
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
     * @SWG\Tag(name="Admin\Users")
     * @Security(name="Bearer")
     */
    public function listUsers()
    {
        $users = $this->userRepository->getUsers();
        return new JsonResponse(array_map(function ($u) {
            return $this->userFormatter->formatBasic($u);
        }, $users));
    }

    /**
     * Create a user
     *
     * @Route("/user", name="create", methods={"POST"})
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
     * @SWG\Tag(name="Admin\Users")
     * @Security(name="Bearer")
     */
    public function createUser(Request $request)
    {
        $email = trim($request->request->get("email"));
        $firstName = trim($request->request->get("firstName"));
        $lastName = trim($request->request->get("lastName"));
        $password = $request->request->get("password");

        $errors = [];

        if (!Validator::validateEmail($email)) {
            $errors['email'] = "L'email è in un formato non valido";
        } elseif (null !== $this->userRepository->getUserByEmail($email)) {
            $errors['email'] = 'Email già esistente';
        }

        if (!Validator::validatePassword($password)) {
            $errors['password'] = "La password deve essere di almeno 6 caratteri e contenere almeno una lettera maiuscola, una minuscola ed un numero";
        }

        if (empty($errors)) {
            $user = new User();
            $user->setEmail($email);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->securePassword($password);

            $this->eventDispatcher->dispatch(new UserEvent($user), UserEvent::BEFORE_CREATE);
            $this->userRepository->save($user);
            $this->eventDispatcher->dispatch(new UserEvent($user), UserEvent::CREATED);

            return new JsonResponse($this->userFormatter->formatBasic($user));
        } else {
            return new JsonResponse([
                "errors" => $errors
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
