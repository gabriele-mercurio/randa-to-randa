<?php

namespace App\Controller;

use App\Entity\User;
use App\Formatter\UserFormatter;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class MeController extends AbstractController
{
    /** @var UserFormatter */
    private $formatter;

    /** @param UserFormatter $formatter */
    public function __construct(UserFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Get me
     * 
     * @Route(path="/me", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Returns a User object representing me",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="id", type="integer"),
     *         @SWG\Property(property="username", type="string")
     *     )
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     * 
     * @return Response
     */
    public function getMeAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse($this->formatter->formatFull($user));
    }
}
