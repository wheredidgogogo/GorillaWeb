<?php

namespace Gorilla\GraphQL;

use Tightenco\Collect\Support\Arr;

/**
 * Class MutationField
 *
 * @package Gorilla\GraphQL
 */
class MutationField extends Filter
{
    /**
     * @return string
     */
    public function text()
    {
        $name = $this->isSubFilter() ? Arr::last(array_keys($this->value)) : $this->name;
        $value = $this->isSubFilter() ? end($this->value) : $this->value;
        $value = json_encode($value);

        return "{$name}: {$value}";
    }

    public function isSubFilter()
    {
        return false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return preg_replace(
            '/"([a-zA-Z]+[a-zA-Z0-9_]*)":/',
            '$1:',
            $this->text()
        );
    }
}
