<?php

namespace Gorilla\GraphQL;

/**
 * Class Builder
 *
 * @package Gorilla\GraphQL
 */
/**
 * Class Builder
 *
 * @package Gorilla\GraphQL
 */
/**
 * Class Builder
 *
 * @package Gorilla\GraphQL
 */
/**
 * Class Builder
 *
 * @package Gorilla\GraphQL
 */
class Builder
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    private $filters = [];

    /**
     * Builder constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return <<<EOF
    {$this->name} {$this->buildFilters($this->getBaseFilter())}{
        {$this->buildFields($this->fields)}
    }
EOF;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param array $filters
     *
     * @return $this
     */
    public function filters(array $filters)
    {
        foreach ($filters as $key => $filter) {
            $this->filters[] = new Filter($key, $filter);
        }

        return $this;
    }

    /**
     * @param $filter
     *
     * @return string
     */
    private function buildFilters(\Illuminate\Support\Collection $filter)
    {
        if ($filter->isEmpty()) {
            return '';
        }

        $string = $filter
            ->map(function (Filter $filter) {
                return (string)$filter;
            })
            ->implode(',');

        return "({$string}) ";
    }

    /**
     * @param      $fields
     *
     * @param null|string $parent
     *
     * @return string
     */
    private function buildFields($fields, $parent = null)
    {
        if (count($fields) === 0) {
            return '';
        }

        $query = '';

        foreach ($fields as $key => $value) {
            if (is_string($key)) {
                $parent = $parent ? "{$parent}.{$key}" : $key;
                $subFilters = $this->getSubFilter($parent);
                $query .= "{$key} {$this->buildFilters($subFilters)}{ {$this->buildFields($value, $parent)} },".PHP_EOL;
                continue;
            }

            $query .= "{$value},".PHP_EOL;
        }

        return $query;
    }

    /**
     * @return static
     */
    public function getBaseFilter()
    {
        return collect($this->filters)->filter(function (Filter $filter) {
            return !$filter->isSubFilter();
        });
    }

    /**
     * @param $path
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSubFilter($path)
    {
        return collect($this->filters)->filter(function (Filter $filter) use ($path) {
           return $filter->getName() === $path;
        });
    }

    /**
     * @return string
     */
    public function cacheKey()
    {
        return md5((string)$this);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}