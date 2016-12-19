<?php

namespace PhalconApi\Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use PhalconApi\Constants\Services;
use PhalconApi\Mvc\Plugin;

class AuthenticationMiddleware extends Plugin implements MiddlewareInterface
{
    public function beforeExecuteRoute()
    {
        $token = $this->di->get(Services::REQUEST)->getToken();

        if ($token) {
            $this->di->get(Services::AUTH_MANAGER)->authenticateToken($token);
        }
    }

    public function call(Micro $api)
    {
        return true;
    }
}
