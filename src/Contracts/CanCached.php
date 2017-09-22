<?php

namespace Gorilla\Contracts;

interface CanCached
{
    public function bootCached();

    public function getCached();

    public function merge(array $array);
}
