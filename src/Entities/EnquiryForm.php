<?php

namespace Gorilla\Entities;

use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;
use Gorilla\Exceptions\NotFoundModelException;

/**
 * Class EnquiryForm
 *
 * @package Gorilla\Entities
 */
class EnquiryForm extends EntityAbstract
{
    /**
     * @var string
     */
    private $name;

    /**
     * constructor.
     *
     * @param $arguments
     *
     */
    public function __construct($arguments = [])
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
        $defaultRoutes = '/website/enquiries';

        if ($this->name) {
            $defaultRoutes = "{$defaultRoutes}/{$this->name}";
        }

        return $defaultRoutes;
    }

    /**
     * @return \Gorilla\Response\JsonResponse
     * @throws \Gorilla\Exceptions\NotFoundModelException
     */
    public function save($attributes)
    {
        if ($this->name) {
            return (new SubmitEnquiry([$this->name], $this->request))->save($attributes);
        }

        throw new NotFoundModelException();
    }
}