<?php

namespace Gorilla\Contracts;

/**
 * Interface RequestInterface
 *
 * @package Gorilla\Contracts
 */
interface RequestInterface
{
    /**
     * @param EntityInterface $entity
     *
     * @return mixed
     */
    public function request(EntityInterface $entity);
}
