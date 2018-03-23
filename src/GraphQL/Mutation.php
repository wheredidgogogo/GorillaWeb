<?php

namespace Gorilla\GraphQL;

use Tightenco\Collect\Support\Collection;

/**
 * Class Mutation
 *
 * @package Gorilla\GraphQL
 */
class Mutation extends Builder
{
    /**
     * @return string
     */
    public function __toString()
    {
        return <<<EOF
        {$this->name} {$this->buildFieldes($this->fields)}
EOF;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function fields(array $fields)
    {
        foreach ($fields as $key => $field) {
            $this->fields->push(new MutationField($key, $field));
        }

        return $this;
    }

    /**
     * @param Collection $fields
     *
     * @return string
     */
    private function buildFieldes(Collection $fields)
    {
        if ($fields->isEmpty()) {
            return '';
        }

        $string = $fields
            ->map(function (Filter $filter) {
                return (string)$filter;
            })
            ->implode(',');

        return "({$string}) ";
    }
}
