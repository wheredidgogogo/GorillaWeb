<?php

namespace Gorilla\GraphQL;

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
    protected $name;

    /**
     * @var string|array
     */
    protected $value;

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

        if (\is_array($this->value)) {
            if ($this->isValueArray($this->value)) {
                $mapValue = $this->mapValues($this->value);
                $filters[] = "{$this->name}: {$mapValue}";
            } else {
                foreach ($this->value as $key => $value) {
                    $mapValue = $this->mapValues($value);
                    $filters[] = "{$key}: {$mapValue}";
                }
            }
        } else {
            $mapValue = $this->mapValues($this->value);
            $filters[] = "{$this->name}: {$mapValue}";
        }
        return implode(',', $filters);
    }

    /**
     * Map value
     *
     * @param $value
     *
     * @return string
     */
    private function mapValues($value)
    {
        if (\is_array($value)) {
            $values = [];
            foreach ($value as $key => $item) {
                $values[] = $this->mapValues($item);
            }

            return '['.implode(',', $values).']';
        }
        return \is_string($value) ? "\"{$value}\"" : $value;
    }

    private function isValueArray($value)
    {
        $valueArray = collect($value)->keys()->filter(function ($item) {
            return is_numeric($item);
        })->isNotEmpty();

        if ($valueArray) {
            return true;
        }

        return false;
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
            if (\is_array($value)) {
                $depth = $this->depth($value) + 1;

                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }

        return $max_depth;
    }
}
