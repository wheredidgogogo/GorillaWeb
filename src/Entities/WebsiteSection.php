<?php

namespace Gorilla\Entities;

use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;

/**
 * Class WebsiteSection
 *
 * @package Gorilla\Entities
 */
class WebsiteSection extends EntityAbstract
{
    /**
     * @var mixed
     */
    private $name;

    /**
     * WebsiteSection constructor.
     *
     * @param array $arguments
     */
    public function __construct(array $arguments = [])
    {
        parent::__construct();

        if (count($arguments) > 0) {
            $this->name = $arguments[0];
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
        return $this->buildEndpoint();
    }

    /**
     * @return string
     */
    private function buildEndpoint()
    {
        $defaultRoutes = '/website/contents';


        if ($this->name) {
            $defaultRoutes = "{$defaultRoutes}/{$this->name}";
        }

        return $defaultRoutes;
    }
}
