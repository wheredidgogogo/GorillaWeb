<?php

if (!function_exists('gorilla_value')) {
    /**
     * @param array $array
     * @param       $field
     * @param       $key
     *
     * @return mixed
     */
    function gorilla_value($array, $field, $key)
    {

        return collect(array_wrap($array))->where($field, $key)->first();
    }
}
