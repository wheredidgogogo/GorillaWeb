<?php

namespace Gorilla\GraphQL;

/**
 * Class Query
 *
 * @package Gorilla\GraphQL
 */
class Query extends Builder
{
    /**
     * Query constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
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
}