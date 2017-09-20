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
class Builder
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $fields;

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
        $fields = implode(',', $this->fields);

        return <<<EOF
            {$this->name} {$this->buildFilters()} {
                {$fields}
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
        $this->filters = $filters;

        return $this;
    }

    /**
     * @return string
     */
    private function buildFilters()
    {
        if (count($this->filters) === 0) {
            return '';
        }

        $filters = array_map(function ($key, $value) {
            return "{$key}: \"${value}\"";
        }, array_keys($this->filters), $this->filters);

        $string = implode(',', $filters);

        return "({$string})";
    }
}