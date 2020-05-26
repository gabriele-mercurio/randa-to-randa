<?php

namespace App\OAuth2;

use LogicException;
use OAuth2\GrantType\GrantTypeInterface;
use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;
use OAuth2\ResponseType\AccessTokenInterface;
use OAuth2\Storage\UserCredentialsInterface;

class UserCredentialsGrant implements GrantTypeInterface
{
    /**
     * @var array
     */
    protected $userInfo;

    /**
     * @var UserCredentialsInterface
     */
    protected $storage;

    /**
     * @param UserCredentialsInterface $storage - REQUIRED Storage class for retrieving user credentials information
     */
    public function __construct(UserCredentialsInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return string
     */
    public function getQueryStringIdentifier(): string
    {
        return 'password';
    }

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @return bool|mixed|null
     *
     * @throws LogicException
     */
    public function validateRequest(RequestInterface $request, ResponseInterface $response)
    {
        if (!$request->request('password') || !$request->request('email')) {
            $response->setError(400, 'invalid_request', 'Missing parameters: "email" and "password" required');

            return null;
        }

        if (!$this->storage->checkUserCredentials($request->request('email'), $request->request('password'))) {
            $response->setError(401, 'invalid_grant', 'Invalid email and password combination');

            return null;
        }

        $userInfo = $this->storage->getUserDetails($request->request('email'));

        if (empty($userInfo)) {
            $response->setError(400, 'invalid_grant', 'Unable to retrieve user information');

            return null;
        }

        if (!isset($userInfo['user_id'])) {
            throw new LogicException('you must set the user_id on the array returned by getUserDetails');
        }

        $this->userInfo = $userInfo;

        return true;
    }

    /**
     * Get client id
     *
     * @return mixed|null
     */
    public function getClientId()
    {
        return null;
    }

    /**
     * Get user id
     *
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userInfo['user_id'];
    }

    /**
     * Get scope
     *
     * @return null|string
     */
    public function getScope(): ?string
    {
        return $this->userInfo['scope'] ?? null;
    }

    /**
     * Create access token
     *
     * @param AccessTokenInterface $accessToken
     * @param mixed                $client_id   - client identifier related to the access token.
     * @param mixed                $user_id     - user id associated with the access token
     * @param string               $scope       - scopes to be stored in space-separated string.
     *
     * @return array
     */
    public function createAccessToken(AccessTokenInterface $accessToken, $client_id, $user_id, $scope)
    {
        return $accessToken->createAccessToken($client_id, $user_id, $scope);
    }
}
