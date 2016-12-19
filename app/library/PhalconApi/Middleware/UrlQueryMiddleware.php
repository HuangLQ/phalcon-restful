<?php

namespace PhalconApi\Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use PhalconApi\Constants\Services;
use PhalconApi\Mvc\Plugin;

class UrlQueryMiddleware extends Plugin implements MiddlewareInterface
{
    public function beforeExecuteRoute()
    {
        $params = $this->di->get(Services::REQUEST)->getQuery();
        $query = $this->di->get(Services::URL_QUERY_PARSER)->createQuery($params);

        $this->di->get(Services::QUERY)->merge($query);
    }

    public function call(Micro $api)
    {
        return true;
    }
}
