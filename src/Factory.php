<?php

namespace Gorilla;

use Gorilla\Entities\Menu;

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
     * @return Menu|null
     */
    public static function create($method)
    {
        switch ($method) {
            case 'menus':
                return new Menu();
        }

        return null;
    }
}