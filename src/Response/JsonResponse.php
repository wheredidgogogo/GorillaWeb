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
    private $data;

    /**
     * JsonResponse constructor.
     *
     * @param $data
     *
     * @internal param Response $response
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @param null $name
     *
     * @return mixed
     */
    public function json($name = null)
    {
        return $name ? $this->data[$name] : $this->data;
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
