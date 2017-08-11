<?php

namespace Gorilla\Response;

use GuzzleHttp\Psr7\Response;

/**
 * Class JsonResponse
 *
 * @package Gorilla\Response
 */
class JsonResponse
{
    /**
     * @var Response
     */
    private $response;

    /**
     * JsonResponse constructor.
     *
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @param null $name
     *
     * @return mixed
     */
    public function json($name = null)
    {
        $json = json_decode($this->response->getBody()->getContents(), true);
        return $name ? $json[$name] : $json;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->response, $name], $arguments);
    }
}
