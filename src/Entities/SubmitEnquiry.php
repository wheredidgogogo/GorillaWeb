<?php

namespace Gorilla\Entities;

use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;
use Gorilla\Request;

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
     * @var string
     */
    private $gorillaUserKey;

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var array
     */
    private $tribes = [];

    /**
     * @var array
     */
    private $files = [];

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $created_at;

    /**
     * @var boolean
     */
    private $notifications;

    /**
     * @var string
     */
    private $source;

    /**
     * constructor.
     *
     * @param array   $arguments
     * @param Request $request
     */
    public function __construct(Request $request, $arguments = [])
    {
        parent::__construct();

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
        $data = [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'ip' => $this->ip,
            'mobile' => $this->mobile,
            'fields' => $this->fields,
            'tribes' => $this->tribes,
            'files' => $this->files,
            'source' => $this->source,
            'gorilla_user_key' => $this->gorillaUserKey,
        ];

        if ($this->created_at) {
            $data['created_at'] = $this->created_at;
        }

        if ($this->notifications !== null) {
            $data['notifications'] = $this->notifications;
        }

        return $data;
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
     * @throws \Phpfastcache\Exceptions\PhpfastcacheDriverCheckException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException
     */
    public function save($attributes)
    {
        return $this->fill($attributes)->get()->json();
    }
}
