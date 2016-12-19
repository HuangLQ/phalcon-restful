<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use PhalconRest\Mvc\Plugin;
use PhalconRest\Api;
use PhalconApi\Constants\Services;
use PhalconApi\Constants\ErrorCodes;
use PhalconApi\Exception;

class AuthorizationMiddleware extends Plugin implements MiddlewareInterface
{
    public function beforeExecuteRoute(Event $event, Api $api)
    {
        $collection = $api->getMatchedCollection();
        $endpoint = $api->getMatchedEndpoint();

        if (!$collection || !$endpoint) {
            return;
        }

        $allowed = $this->di->get(Services::ACL)->isAllowed(
            $this->di->get(Services::USER_SERVICE)->getRole(),
            $collection->getIdentifier(),
            $endpoint->getIdentifier()
        );

        if (!$allowed) {
            throw new Exception(ErrorCodes::ACCESS_DENIED);
        }
    }

    public function call(Micro $api)
    {
        return true;
    }
}
