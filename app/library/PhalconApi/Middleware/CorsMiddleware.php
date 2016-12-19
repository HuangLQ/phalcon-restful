<?php

namespace PhalconApi\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use PhalconApi\Constants\Services;
use PhalconApi\Constants\HttpMethods;
use PhalconApi\Mvc\Plugin;

class CorsMiddleware extends Plugin implements MiddlewareInterface
{
    public static $ALL_ORIGINS = ['*'];
    public static $DEFAULT_HEADERS = ['Content-Type', 'X-Requested-With', 'Authorization'];

    /**
     * @var array Allowed origins
     */
    protected $allowedOrigins;

    /**
     * @var array Allowed methods
     */
    protected $allowedMethods;

    /**
     * @var array Allowed headers
     */
    protected $allowedHeaders;

    /**
     * Cors constructor.
     *
     * @param array|null $allowedOrigins Allowed origins
     * @param array|null $allowedMethods Allowed methods
     * @param array|null $allowedHeaders Allowed headers
     */
    public function __construct(
        array $allowedOrigins = null,
        array $allowedMethods = null,
        array $allowedHeaders = null
    ) {
        if ($allowedOrigins === null) {
            $allowedOrigins = self::$ALL_ORIGINS;
        }

        if ($allowedMethods === null) {
            $allowedMethods = HttpMethods::$ALL_METHODS;
        }

        if ($allowedHeaders === null) {
            $allowedHeaders = self::$DEFAULT_HEADERS;
        }

        $this->setAllowedOrigins($allowedOrigins);
        $this->setAllowedMethods($allowedMethods);
        $this->setAllowedHeaders($allowedHeaders);
    }

    public function getAllowedOrigins()
    {
        return $this->allowedOrigins;
    }

    public function setAllowedOrigins(array $allowedOrigins)
    {
        if ($allowedOrigins === null) {
            $allowedOrigins = [];
        }

        $this->allowedOrigins = $allowedOrigins;
    }

    public function addAllowedOrigin($origin)
    {
        $this->allowedOrigins[] = $origin;
    }

    public function getAllowedMethods()
    {
        return $this->allowedMethods;
    }

    public function setAllowedMethods(array $allowedMethods)
    {
        if ($allowedMethods === null) {
            $allowedMethods = [];
        }

        $this->allowedMethods = $allowedMethods;
    }

    public function addAllowedMethod($method)
    {
        $this->allowedMethods[] = $method;
    }

    public function getAllowedHeaders()
    {
        return $this->allowedHeaders;
    }

    public function setAllowedHeaders(array $allowedHeaders)
    {
        if ($allowedHeaders === null) {
            $allowedHeaders = [];
        }

        $this->allowedHeaders = $allowedHeaders;
    }

    public function addAllowedHeader($header)
    {
        $this->allowedHeaders[] = $header;
    }

    public function beforeHandleRoute(Event $event, \PhalconApi\Api $api)
    {
        if (count($this->allowedOrigins) == 0) {
            return;
        }

        // Origin
        $originIsWildcard = in_array('*', $this->allowedOrigins);
        $originValue = null;

        if ($originIsWildcard) {
            $originValue = '*';
        } else {
            $origin = $this->request->getHeader('Origin');
            $originDomain = $origin ? parse_url($origin, PHP_URL_HOST) : null;

            if ($originDomain) {
                $allowed = false;

                foreach ($this->allowedOrigins as $allowedOrigin) {
                    // First try exact domain
                    if ($originDomain == $allowedOrigin) {
                        $allowed = true;
                        break;
                    }

                    // Parse wildcards
                    $expression = '/^' . str_replace('\*', '(.+)', preg_quote($allowedOrigin, '/')) . '$/';
                    if (preg_match($expression, $originDomain) == 1) {
                        $allowed = true;
                        break;
                    }
                }

                if ($allowed) {
                    $originValue = $origin;
                }
            }
        }

        if ($originValue != null) {
            $this->di->get(Services::RESPONSE)->setHeader('Access-Control-Allow-Origin', $originValue);

            // Allowed methods
            if (count($this->allowedMethods) > 0) {
                $this->di->get(Services::RESPONSE)->setHeader('Access-Control-Allow-Methods', implode(',', $this->allowedMethods));
            }

            // Allowed headers
            if (count($this->allowedHeaders) > 0) {
                $this->di->get(Services::RESPONSE)->setHeader('Access-Control-Allow-Headers', implode(',', $this->allowedHeaders));
            }
        }
    }

    public function call(Micro $api)
    {
        return true;
    }
}
