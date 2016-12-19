<?php

namespace PhalconRest\Mvc\Controllers;

class CollectionController extends FractalController
{
    /** @var \PhalconRest\Api\ApiCollection */
    protected $collection;

    /** @var \PhalconRest\Api\ApiEndpoint */
    protected $endpoint;

    /**
     * @return \PhalconRest\Api\ApiCollection
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->application->getMatchedCollection();
        }

        return $this->collection;
    }

    /**
     * @return \PhalconRest\Api\ApiEndpoint
     */
    public function getEndpoint()
    {
        if (!$this->endpoint) {
            $this->endpoint = $this->application->getMatchedEndpoint();
        }

        return $this->endpoint;
    }
}
