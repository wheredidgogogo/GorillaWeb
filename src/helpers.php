<?php

use Illuminate\Support\Arr;

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
        return collect(Arr::wrap($array))->where($field, $key)->filter()->first();
    }
}

if (!function_exists('gorillaValue')) {
    /**
     * @param array $array
     * @param       $field
     * @param       $key
     *
     * @return mixed
     */
    function gorillaValue($array, $field, $key)
    {
        return collect(Arr::wrap($array))->where($field, $key)->filter()->first();
    }
}
