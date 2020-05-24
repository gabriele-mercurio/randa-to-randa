<?php

namespace App\Guard;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use OAuth2\Request as OAuth2Request;
use OAuth2\Server;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /** @var Server */
    private $oauth2Server;

    /** @var UserRepository */
    private $userRepository;

    /** @var User currentUser */
    private $currentUser;
    
    /** @var mixed currentAccessToken */
    private $currentAccessToken;

    /**
     * TokenAuthenticator constructor.
     *
     * @param Server                 $server
     * @param UserRepository $userRepository
     */
    public function __construct(Server $server, UserRepository $userRepository)
    {
        $this->oauth2Server  = $server;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return string
     */
    private function getRoute(Request $request) {
        return $request->attributes->get("_route");
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        // leggo la rotta corrente, perché token deve passare senza essere gestita
        if ($this->getRoute($request) === 'token' && $request->isMethod('POST')) {
            return false;
        }

        /** @var string $token */
        $token = $request->headers->get('Authorization', '');
        return preg_match("/^bearer\s+[0-9a-f]{40}$/i", $token, $matches);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request): array
    {
        /** @var string $token */
        $token = $request->headers->get('Authorization', '');
        if(preg_match("/^bearer\s+(?<token>[0-9a-f]{40})$/i", $token, $matches)) {
            $token = $matches['token'];
        } else {
            $token = null;
        }
        
        return [
            'token' => $token
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?User
    {
        if (!$this->oauth2Server->verifyResourceRequest(OAuth2Request::createFromGlobals())) {
            return null;
        }

        $access_token_info = $this->oauth2Server->getAccessTokenData(OAuth2Request::createFromGlobals());
        $user_id  = $access_token_info['user_id'];

        try {
            /** @var User $user */
            $user = $this->userRepository->findOneBy([
                'id' => $user_id
            ]);

            if($user) {
                $this->currentUser = $user;
                $this->currentAccessToken = $access_token_info;
            } else {
                $this->currentUser = null;
                $this->currentAccessToken = null;
            }

            return $user;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        if($this->currentAccessToken) {
            $request->attributes->set("oauth_access_token", $this->currentAccessToken['access_token']);
            $request->attributes->set("oauth_client_id", $this->currentAccessToken['client_id']);
        }
        return null;
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $data = [
            'message' => 'You need to provide a valid OAuth2 token to access this resource'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
