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
    public function method(): string
    {
        return MethodType::POST;
    }

    /**
     * Request parameters
     *
     * @return array
     */
    public function parameters(): array
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
    public function endpoint(): string
    {
        return '/graphql';
    }

    /**
     * @return array
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     */
    public function getCached(): array
    {
        return $this->cacheData = $this->collection->getQueries()
            ->mapWithKeys(function (Query $query) {
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
    public function allInCached(): bool
    {
        return $this->collection->getQueries()->isEmpty();
    }

    /**
     * Save cache
     *
     * @return mixed
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     */
    public function saveCache($data)
    {
        collect($data)->each(function ($value, $key) {
            /** @var Query $query */
            $query = $this->collection->find($key);
            if ($query) {
                $this->saveCacheData($query->cacheKey(), $value);
            }
        });
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public function merge(array $array)
    {
        return array_merge_recursive(['data' => $this->cacheData], $array);
    }
}
