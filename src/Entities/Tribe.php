<?php


namespace Gorilla\Entities;


use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;

/**
 * Class Tribe
 *
 * @package Gorilla\Entities
 */
class Tribe extends EntityAbstract
{
    /**
     * @var mixed
     */
    private $name;

    /**
     * @var
     */
    private $slug;

    /**
     * WebsiteSection constructor.
     *
     * @param array $arguments
     */
    public function __construct(array $arguments = [])
    {
        parent::__construct($arguments);

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
        $defaultRoutes = '/website/tribes';

        if ($this->name) {
            $defaultRoutes = "{$defaultRoutes}/{$this->name}";
        }

        if ($this->slug) {
            $defaultRoutes = "{$defaultRoutes}/slug/{$this->slug}";
        }

        return $defaultRoutes;
    }

    /**
     * @param $slug
     *
     * @return $this
     */
    public function slug($slug)
    {
        $this->slug = $slug;

        return $this;
    }
}