<?php

namespace Gorilla\Entities;

use Gorilla\Contracts\CanCached;
use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;
use Gorilla\GraphQL\Builder;
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
        parent::__construct();
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
     * @return stringgetCached
     */
    public function endpoint(): string
    {
        return '/graphql';
    }

    /**
     * Cache query data (Only for Query)
     *
     * @return array
     */
    public function getCached(): array
    {
        $this->cacheData = $this->collection->getQueries()
            ->filter(function (Builder $query) {
                return $query instanceof Query && $query->getName() !== 'lastUpdatedAt';
            })
            ->mapWithKeys(function (Builder $query) {
                $cacheTime = $this->getCachedTime($query->cacheKey());
                if ($cacheTime && $cacheTime['lastUpdatedAt'] === $this->getLastUpdatedAt()) {
                    return [
                        $query->getName() => $this->getCacheContent($query->cacheKey()),
                    ];
                }
                return [$query->getName() => null];
            })->filter(function ($value) {
                return $value;
            })->each(function ($value, $key) {
                $this->collection->removeQuery($key);
            })->toArray();


        return $this->cacheData;
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
     * @param $data
     *
     * @return mixed
     */
    public function saveCache($data)
    {
        if ($this->collection->find('lastUpdatedAt')) {
            return;
        }
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
