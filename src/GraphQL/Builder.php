<?php

namespace Gorilla\GraphQL;

use Tightenco\Collect\Support\Collection;

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
     * @var Collection
     */
    protected $fields;

    /**
     * @var Collection
     */
    private $filters;

    /**
     * Builder constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->filters = new Collection();
        $this->fields = new Collection();
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
        $this->fields = collect($fields);

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
            $this->filters->push(new Filter($key, $filter));
        }

        return $this;
    }

    /**
     * @param Collection $filter
     *
     * @return string
     */
    private function buildFilters($filter)
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
     * @param Collection  $fields
     *
     * @param null|string $parent
     *
     * @return string
     */
    private function buildFields($fields, $parent = null)
    {
        if ($fields->isEmpty()) {
            return '';
        }

        $query = '';

        $fields->each(function ($value, $key) use (&$query, $parent) {
            if (is_string($key)) {
                $parent = $parent ? "{$parent}.{$key}" : $key;
                $subFilters = $this->getSubFilter($parent);
                $query .= "{$key} {$this->buildFilters($subFilters)}{ {$this->buildFields(collect($value), $parent)} },"
                    .PHP_EOL;
                return true;
            }

            $query .= "{$value},".PHP_EOL;
        });

        return $query;
    }

    /**
     * @return Collection
     */
    public function getBaseFilter()
    {
        $fields = $this->fields->keys()->toArray();
        return $this->filters->filter(function (Filter $filter) use ($fields) {
            return !$filter->isSubFilter() && !\in_array($filter->getName(), $fields, true);
        });
    }

    /**
     * @param $path
     *
     * @return Collection
     */
    public function getSubFilter($path)
    {
        return $this->filters->filter(function (Filter $filter) use ($path) {
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
