<?php

namespace Gorilla\Entities;

use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;
use Gorilla\Request;

/**
 * Class SubMenu
 *
 * @package Gorilla\Entities
 */
class SubMenu extends EntityAbstract
{
    /**
     * @var integer
     */
    private $id;

    /**
     * Category constructor.
     *
     * @param array   $arguments
     * @param Request $request
     */
    public function __construct($arguments = [], Request $request)
    {
        parent::__construct($arguments);

        if (count($arguments) > 0) {
            $this->id = $arguments[0];
        }
        $this->setRequest($request);
    }
    /**
     * Request method type
     *
     * @return string
     */
    public function method()
    {
        return MethodType::GET;
    }

    /**
     * Request parameters
     *
     * @return array
     */
    public function parameters()
    {
        return [];
    }

    /**
     * Endpoint url
     *
     * @return string
     */
    public function endpoint()
    {
        return "/website/menus/sub/{$this->id}";
    }
}