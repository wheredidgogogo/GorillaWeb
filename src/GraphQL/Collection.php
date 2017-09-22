<?php

namespace Gorilla\GraphQL;

/**
 * Class Collection
 *
 * @package Gorilla\GraphQL
 */
/**
 * Class Collection
 *
 * @package Gorilla\GraphQL
 */
class Collection
{
    /**
     * @var string
     */
    private $method = 'query';

    /**
     * @var array
     */
    private $queries = [];

    /**
     * @var Query
     */
    private $current;

    /**
     * @param $name
     *
     * @return $this
     */
    public function query($name)
    {
        $this->setMethod('query');

        $query = new Query($name);
        $this->queries[] = $query;
        $this->current = $query;

        return $this;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function fields(array $fields)
    {
        $this->current->fields($fields);

        return $this;
    }

    /**
     * @param array $filter
     *
     * @return $this
     */
    public function filters(array $filter)
    {
        $this->current->filters($filter);

        return $this;
    }

    /**
     * @param $method
     *
     * @return $this
     */
    private function setMethod($method)
    {
        if ($this->method !== $method) {
            $this->reset();
        }
        $this->method = $method;

        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->queries = [];
        $this->current = null;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $queryString = '';

        foreach ($this->queries as $query) {
            $queryString .= (string)$query;
        }
        return "{$this->method} { {$queryString} }";
    }

    /**
     * @return array|Query[]
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @param $name
     *
     * @return Query|null
     */
    public function find($name)
    {
        return collect($this->queries)->filter(function ($query) use ($name) {
            return $query->getName() === $name;
        })->first();
    }

    /**
     * @return Query
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param $key
     */
    public function removeQuery($key)
    {
        $this->queries = collect($this->queries)->reject(function (Query $query) use ($key) {
            return $query->getName() === $key;
        })->toArray();
    }
}