<?php

namespace PhalconApi\Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use PhalconApi\Constants\Services;
use PhalconApi\Mvc\Plugin;

class OptionsResponseMiddleware extends Plugin implements MiddlewareInterface
{
    public function beforeHandleRoute()
    {
        // OPTIONS request, just send the headers and respond OK
        if ($this->di->get(Services::REQUEST)->isOptions()) {
            $this->di->get(Services::RESPONSE)->setJsonContent([
                'result' => 'OK',
            ]);

            return false;
        }
    }

    public function call(Micro $api)
    {
        return true;
    }
}
