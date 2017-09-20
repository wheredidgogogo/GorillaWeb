<?php

namespace Gorilla\Entities;

use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;
use Gorilla\GraphQL\Collection;

class GraphQL extends EntityAbstract
{
    /**
     * @var Collection
     */
    private $collection;

    public function __construct(Collection $collection)
    {
        parent::__construct([]);
        $this->collection = $collection;
    }

    /**
     * Request method type
     *
     * @return string
     */
    public function method()
    {
        return MethodType::POST;
    }

    /**
     * Request parameters
     *
     * @return array
     */
    public function parameters()
    {
        return [
            'query' => (string)$this->collection,
        ];
    }

    /**
     * Endpoint url
     *
     * @return string
     */
    public function endpoint()
    {
        return '/graphql';
    }
}