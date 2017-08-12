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
     * @param $params
     *
     * @return Menu|null
     */
    public static function create($method, $params)
    {
        switch ($method) {
            case 'menus':
                return new Menu($params);
        }

        return null;
    }
}