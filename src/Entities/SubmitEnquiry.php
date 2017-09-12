<?php

namespace Gorilla\Entities;

use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;
use Gorilla\Request;
use InvalidArgumentException;

/**
 * Class SubmitEnquiry
 *
 * @package Gorilla\Entities
 */
class SubmitEnquiry extends EntityAbstract
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $mobile;
    
    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var array
     */
    private $tribes = [];

    /**
     * @var string
     */
    private $ip;

    /**
     * constructor.
     *
     * @param array   $arguments
     * @param Request $request
     */
    public function __construct($arguments = [], Request $request)
    {
        parent::__construct($arguments);

        if (count($arguments) > 0) {
            $this->name = $arguments[0];
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
        return MethodType::POST;
    }

    /**
     * Request parameters
     *
     * @return array
     */
    public function parameters()
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'ip' => $this->ip,
            'mobile' => $this->mobile,
            'fields' => $this->fields,
            'tribes' => $this->tribes,
        ];
    }

    /**
     * Endpoint url
     *
     * @return string
     */
    public function endpoint()
    {
        return "/website/enquiries/{$this->name}";
    }

    /**
     * @param $attributes
     *
     * @return $this
     */
    public function fill($attributes)
    {
        foreach ($attributes as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->{$attribute} = $value;
            }
        }

        return $this;
    }

    /**
     * @param $attributes
     *
     * @return \Gorilla\Response\JsonResponse
     */
    public function save($attributes)
    {
        return $this->fill($attributes)->get()->json();
    }
}