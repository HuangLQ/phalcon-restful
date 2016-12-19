<?php

namespace PhalconRest\Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use PhalconRest\Constants\Services as PhalconRestServices;
use PhalconRest\Mvc\Plugin;
use PhalconApi\Constants\Services as PhalconApiServices;

class FractalMiddleware extends Plugin implements MiddlewareInterface
{
    public $parseIncludes;

    public function __construct($parseIncludes = true)
    {
        $this->parseIncludes = $parseIncludes;
    }

    public function beforeExecuteRoute()
    {
        /** @var \League\Fractal\Manager $fractal */
        $fractal = $this->di->get(PhalconRestServices::FRACTAL_MANAGER);

        if ($this->parseIncludes) {
            $include = $this->di->get(PhalconApiServices::RESPONSE)->getQuery('include');

            if (!is_null($include)) {
                $fractal->parseIncludes($include);
            }
        }
    }

    public function call(Micro $api)
    {
        return true;
    }
}
