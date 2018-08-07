<?php

namespace Gorilla\Entities;

/**
 * Class AccessToken
 *
 * @package Gorilla\Tokens
 */

use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;

/**
 * Class AccessToken
 *
 * @package Gorilla\Tokens
 */
class AccessToken extends EntityAbstract
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $token;

    /**
     * AccessToken constructor.
     *
     * @param $id
     * @param $token
     */
    public function __construct($id, $token)
    {
        $this->id = $id;
        $this->token = $token;
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
            'grant_type' => 'client_credentials',
            'client_id' => $this->id,
            'client_secret' => $this->token,
            'scope' => '',
        ];
    }

    /**
     * Endpoint url
     *
     * @return string
     */
    public function endpoint()
    {
        return '/oauth/token';
    }
}
