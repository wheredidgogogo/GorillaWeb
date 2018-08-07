<?php

namespace Gorilla\Entities;

use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;

/**
 * Class ProductComponent
 *
 * @package Gorilla\Entities
 */
class ProductComponent extends EntityAbstract
{
    /**
     * @var mixed
     */
    private $componentType;

    /**
     * Category constructor.
     *
     * @param $arguments
     *
     */
    public function __construct($arguments = [])
    {
        parent::__construct();

        if (count($arguments) > 0) {
            $this->componentType = $arguments[0];
        }
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
        return "/website/productComponents/{$this->componentType}";
    }
}
