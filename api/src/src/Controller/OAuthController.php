<?php

namespace App\Controller;

use App\OAuth2\Storage;
use OAuth2\Request as OAuth2Request;
use OAuth2\Response as OAuth2Response;
use OAuth2\Server as OAuth2Server;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends AbstractController
{
    /** @var OAuth2Server */
    private $server;

    /**
     * OAuthController constructor.
     *
     * @param OAuth2Server $server
     */
    public function __construct(OAuth2Server $server)
    {
        $this->server = $server;
    }
    
    /**
     * Request a token
     * 
     * @Route(path="/token", name="token", methods="POST")
     * @SWG\Parameter(
     *     name="client_id",
     *     in="formData",
     *     type="string",
     *     description="The idefyier of the OAuth client",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="client_secret",
     *     in="formData",
     *     type="string",
     *     description="The secret key of the OAuth client",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="grant_type",
     *     in="formData",
     *     type="string",
     *     description="The type of the grant required, normally 'password'",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="username",
     *     in="formData",
     *     type="string",
     *     description="The identifier of the user that is requiring the token",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     type="string",
     *     description="The password of the user that is requiring the token",
     *     required=true
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns a valid bearer token",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="access_token", type="string"),
     *         @SWG\Property(property="expires_in", type="integer"),
     *         @SWG\Property(property="token_type", type="string"),
     *         @SWG\Property(property="scope", type="string"),
     *         @SWG\Property(property="refresh_token", type="string")
     *     )
     * )
     * @SWG\Tag(name="OAuth")
     * @Security(name="none")
     */
    public function tokenAction(): void
    {

        $response = new OAuth2Response([], 200, ['Access-Control-Allow-Origin' => '*']);

        /** @var OAuth2Response $response */
        $response = $this->server->handleTokenRequest(OAuth2Request::createFromGlobals(), $response);
        $response->send();
        die();
    }

    /**
     * Revoke a token
     * 
     * @Route(path="/revoke", name="revoke", methods={"POST"})
     * @IsGranted("ROLE_USER")
     * 
     * @SWG\Parameter(
     *     name="oauth_access_token",
     *     in="formData",
     *     type="string",
     *     description="The actual vaild Bearer token"
     * )
     * @SWG\Parameter(
     *     name="oauth_client_id",
     *     in="formData",
     *     type="string",
     *     description="The OAuth client identifyier"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Revoke the token for the current user",
     * )
     * @SWG\Tag(name="OAuth")
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function revokeAction(Request $request): Response
    {
        $token = $request->attributes->get("oauth_access_token");
        $clientId = $request->attributes->get("oauth_client_id");

        /** @var Storage $storage */
        $storage = $this->server->getStorage("user_credentials");
        if (!is_null($storage) && $token && $clientId) {
            $storage->unsetRefreshToken($token);
            $storage->unsetAccessToken($token, $clientId);
        }

        return new JsonResponse(
            null,
            200,
            ['Access-Control-Allow-Origin' => '*']
        );
    }
}
