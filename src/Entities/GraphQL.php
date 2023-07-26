<?php

namespace Gorilla\Entities;

use Carbon\Carbon;
use Gorilla\Contracts\CanCached;
use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;
use Gorilla\GraphQL\Builder;
use Gorilla\GraphQL\Collection;
use Gorilla\GraphQL\Mutation;
use Gorilla\GraphQL\Query;
use Gorilla\Traits\Cacheable;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use Psr\Cache\InvalidArgumentException;

class GraphQL extends EntityAbstract implements CanCached
{
    use Cacheable {
        getCached as getCacheContent;
    }

    /**
     * @var Collection
     */
    private $collection;

    private $fromApi = false;

    /**
     * GraphQL constructor.
     *
     * @param  Collection  $collection
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
            'query' => (string) $this->collection,
        ];
    }

    /**
     * @return bool
     */
    public function isQuery()
    {
        return $this->collection
            ->getQueries()
            ->filter(function (Builder $query) {
                return $query instanceof Mutation;
            })
            ->isEmpty();
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
     * Cache query data (Only for Query)
     *
     * @return array
     * @throws PhpfastcacheInvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function getCached(): array
    {
        if ($this->fromApi) {
            return $this->cacheData;
        }

        $this->cacheData = $this->collection
            ->getQueries()
            ->filter(function (Builder $query) {
                return $query instanceof Query;
            })
            ->mapWithKeys(function (Builder $query) {
                return [
                    $query->getName() => $this->getCacheContent($query->cacheKey()),
                ];
            })
            ->filter(function ($value) {
                return $value;
            })
            ->each(function ($value, $key) {
                $this->collection->removeQuery($key);
            })
            ->toArray();

        return $this->cacheData;
    }

    /**
     * @return $this
     */
    public function withoutCacheContent()
    {
        $this->fromApi = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function allInCached(): bool
    {
        if ($this->fromApi) {
            return false;
        }
        return $this->collection->getQueries()->isEmpty();
    }

    /**
     * Save cache
     *
     * @param $data
     *
     * @return mixed
     * @throws PhpfastcacheInvalidArgumentException
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
     * @param  array  $array
     *
     * @return array
     */
    public function merge(array $array)
    {
        return array_merge_recursive(['data' => $this->cacheData], $array);
    }
}
