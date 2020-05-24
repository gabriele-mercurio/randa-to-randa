<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Events\UserEvent;
use App\Formatter\UserFormatter;
use App\Repository\UserRepository;
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
    private $repository;

    /** @var UserFormatter */
    private $formatter;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        UserRepository $repository,
        UserFormatter $formatter,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->repository = $repository;
        $this->formatter = $formatter;
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
     *              @SWG\Property(property="id", type="integer"),
     *              @SWG\Property(property="username", type="string")
     *          )
     *      )
     * )
     * @SWG\Tag(name="Admin\Users")
     * @Security(name="Bearer")
     */
    public function listUsers()
    {
        $users = $this->repository->getUsers();
        return new JsonResponse(array_map(function ($u) {
            return $this->formatter->format($u);
        }, $users));
    }

    /**
     * Create a user
     * 
     * @Route("/user", name="create", methods={"POST"})
     * 
     * @SWG\Parameter(
     *      name="username",
     *      in="formData",
     *      type="string",
     *      description="The User's username",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="password",
     *      in="formData",
     *      type="string",
     *      description="The User's password",
     *      required=true
     * )
     * @SWG\Parameter(
     *      name="role",
     *      in="formData",
     *      type="array",
     *      items={"type"="string"},
     *      description="The User's role"
     * )
     * @SWG\Response(
     *      response=201,
     *      description="Returns the created user",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="id", type="integer"),
     *          @SWG\Property(property="username", type="string")
     *      )
     * )
     * @SWG\Tag(name="Admin\Users")
     * @Security(name="Bearer")
     */
    public function createUser(Request $request)
    {
        $username = trim($request->request->get("username"));
        $password = $request->request->get("password");
        $roles = $request->request->get("roles");

        $errors = [];

        if (($uv = static::validateUsername($username)) !== true) {
            $errors['username'] = $uv;
        } elseif (null !== $this->repository->getUserByUsername($username)) {
            $errors['username'] = 'Username già esistente';
        }

        if (($pv = static::validatePassword($password)) !== true) {
            $errors['password'] = $pv;
        }

        if (empty($errors)) {
            $user = new User();
            $user->setUsername($username);
            $user->setPassword($password);
            $user->setRoles($roles);

            $this->eventDispatcher->dispatch(new UserEvent($user), UserEvent::BEFORE_CREATE);
            $this->repository->save($user);
            $this->eventDispatcher->dispatch(new UserEvent($user), UserEvent::CREATED);

            return new JsonResponse($this->formatter->format($user));
        } else {
            return new JsonResponse([
                "errors" => $errors
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Validators
     */

    private static function validateUsername($username)
    {
        if (!preg_match("/^(?=.{6,}).*/", $username)) {
            return "L'username deve essere di almeno 6 caratteri";
        }
        return true;
    }

    private static function validatePassword($password)
    {
        if (!preg_match("/^(?=.{6,})(?=[^0-9]*[0-9])(?=[^a-z]*[a-z])(?=[^A-Z]*[A-Z]).*/", $password)) {
            return "La password deve essere di almeno 6 caratteri e contenere almeno una lettera maiuscola, una minuscola ed un numero";
        }
        return true;
    }
}
