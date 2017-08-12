<?php

namespace Gorilla;

use Gorilla\Contracts\EntityInterface;
use Gorilla\Entities\Menu;
use Gorilla\Entities\Product;

/**
 * Class Factory
 *
 * @package Gorilla
 */
class Factory
{
    /**
     * @param $method
     *
     * @return EntityInterface
     */
    public static function create($method)
    {
        switch ($method) {
            case 'menus':
                return new Menu();
            case 'products':
                return new Product();
        }

        return null;
    }
}