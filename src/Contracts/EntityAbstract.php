<?php

namespace Gorilla\Contracts;

/**
 * Class EntityAbstract
 *
 * @package Gorilla\Contracts
 */
/**
 * Class EntityAbstract
 *
 * @package Gorilla\Contracts
 */
/**
 * Class EntityAbstract
 *
 * @package Gorilla\Contracts
 */
abstract class EntityAbstract implements EntityInterface
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * EntityAbstract constructor.
     *
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        $this->parameters = $parameters;
    }
}
