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
        $filters = [];
        if ($this->isSubFilter()) {
            foreach ($this->value as $key => $value) {
                if (is_array($value)) {
                    $array = implode(',', collect($value)->map(function ($value) {
                        return is_string($value) ? "\"{$value}\"" : $value;
                    })->toArray());
                    $value = "[{$array}]";
                }
                $filters[] = "{$key}: {$value}";
            }
            return implode(',', $filters);
        }

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
        return is_array($this->value) && $this->depth($this->value) > 1;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $array
     *
     * @return int
     */
    private function depth(array $array)
    {
        $max_depth = 1;

        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = $this->depth($value) + 1;

                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }

        return $max_depth;
    }
}
