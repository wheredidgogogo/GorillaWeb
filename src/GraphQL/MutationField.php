<?php

namespace Gorilla\GraphQL;

/**
 * Class MutationField
 *
 * @package Gorilla\GraphQL
 */
class MutationField extends Filter
{
    /**
     * @return bool
     */
    public function isSubFilter()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return preg_replace(
            '/"([a-zA-Z]+[a-zA-Z0-9_]*)":/',
            '$1:',
            parent::__toString()
        );
    }
}
