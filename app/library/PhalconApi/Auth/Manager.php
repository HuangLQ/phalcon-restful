<?php

namespace PhalconApi\Auth;

use PhalconApi\Constants\ErrorCodes;
use PhalconApi\Exception;

class Manager extends \PhalconApi\Mvc\Plugin
{
    const LOGIN_DATA_USERNAME = "username";
    const LOGIN_DATA_PASSWORD = "password";

    /**
     * @var AccountType[] Account types
     */
    protected $accountTypes;

    /**
     * @var Session Currenty active session
     */
    protected $session;

    /**
     * @var int Expiration time of created sessions
     */
    protected $sessionDuration;


    public function __construct($sessionDuration = 86400)
    {
        $this->sessionDuration = $sessionDuration;

        $this->accountTypes = [];
        $this->session = null;
    }


    public function registerAccountType($name, AccountType $account)
    {
        $this->accountTypes[$name] = $account;

        return $this;
    }

    public function getAccountTypes()
    {
        return $this->accountTypes;
    }


    public function getSessionDuration()
    {
        return $this->sessionDuration;
    }

    public function setSessionDuration($time)
    {
        $this->sessionDuration = $time;
    }


    public function getSession()
    {
        return $this->session;
    }

    public function setSession(Session $session)
    {
        $this->session = $session;
    }


    /**
     * @return bool
     *
     * Check if a user is currently logged in
     */
    public function loggedIn()
    {
        return !!$this->session;
    }

    /**
     * @param string $accountTypeName
     * @param string $username
     * @param string $password
     *
     * @return Session Created session
     * @throws Exception
     *
     * Helper to login with username & password
     */
    public function loginWithUsernamePassword($accountTypeName, $username, $password)
    {
        return $this->login($accountTypeName, [

            self::LOGIN_DATA_USERNAME => $username,
            self::LOGIN_DATA_PASSWORD => $password
        ]);
    }

    /**
     * @param string $accountTypeName
     * @param array $data
     *
     * @return Session Created session
     * @throws Exception
     *
     * Login a user with the specified account-type
     */
    public function login($accountTypeName, array $data)
    {
        if (!$account = $this->getAccountType($accountTypeName)) {
            throw new Exception(ErrorCodes::AUTH_INVALID_ACCOUNT_TYPE);
        }

        $identity = $account->login($data);

        if (!$identity) {
            throw new Exception(ErrorCodes::AUTH_LOGIN_FAILED);
        }

        $startTime = time();

        $session = new Session($accountTypeName, $identity, $startTime, $startTime + $this->sessionDuration);
        $token = $this->tokenParser->getToken($session);
        $session->setToken($token);

        $this->session = $session;

        return $this->session;
    }

    /**
     * @param $name
     *
     * @return \PhalconApi\Auth\AccountType Account-type
     */
    public function getAccountType($name)
    {
        if (array_key_exists($name, $this->accountTypes)) {
            return $this->accountTypes[$name];
        }

        return null;
    }

    /**
     * @param string $token Token to authenticate with
     *
     * @return bool
     * @throws Exception
     */
    public function authenticateToken($token)
    {
        try {
            $session = $this->tokenParser->getSession($token);
        } catch (\Exception $e) {
            throw new Exception(ErrorCodes::AUTH_TOKEN_INVALID);
        }

        if (!$session) {
            return false;
        }

        if ($session->getExpirationTime() < time()) {
            throw new Exception(ErrorCodes::AUTH_SESSION_EXPIRED);
        }

        $session->setToken($token);

        // Authenticate identity
        if (!$account = $this->getAccountType($session->getAccountTypeName())) {
            throw new Exception(ErrorCodes::AUTH_SESSION_INVALID);
        }

        if (!$account->authenticate($session->getIdentity())) {
            throw new Exception(ErrorCodes::AUTH_TOKEN_INVALID);
        }

        $this->session = $session;

        return true;
    }
}
