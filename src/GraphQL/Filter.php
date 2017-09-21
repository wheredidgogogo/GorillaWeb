<?php

namespace Gorilla\GraphQL;

/**
 * Class Filter
 *
 * @package Gorilla\GraphQL
 */
use Illuminate\Support\Arr;

/**
 * Class Filter
 *
 * @package Gorilla\GraphQL
 */
class Filter
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string|array
     */
    private $value;

    /**
     * Filter constructor.
     *
     * @param $name
     * @param $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $name = $this->isSubFilter() ? Arr::last(array_keys($this->value)) : $this->name;
        $value = $this->isSubFilter() ? end($this->value) : $this->value;
        $value = json_encode($value);

        return "{$name}: {$value}";
    }

    /**
     * @return bool
     */
    public function isSubFilter()
    {
        return strpos($this->name, '.') > 0;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}