<?php

namespace Gorilla\Entities;

use Gorilla\Contracts\CanCached;
use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;
use Gorilla\GraphQL\Collection;
use Gorilla\GraphQL\Query;
use Gorilla\Traits\Cacheable;

class GraphQL extends EntityAbstract implements CanCached
{
    use Cacheable {
        getCached as getCacheContent;
    }

    /**
     * @var Collection
     */
    private $collection;

    /**
     * GraphQL constructor.
     *
     * @param Collection $collection
     */
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

    /**
     * @return \Illuminate\Support\Collection;
     */
    public function getCached()
    {
        $this->data = collect($this->collection->getQueries())->mapWithKeys(function (Query $query) {
            return [
                $query->getName() => $this->getCacheContent($query->cacheKey()),
            ];
        })->filter(function ($value) {
            return $value;
        })->each(function ($value, $key) {
            $this->collection->removeQuery($key);
        })->toArray();
    }

    /**
     * @return bool
     */
    public function allInCached()
    {
        return count($this->collection->getQueries()) === 0;
    }
}