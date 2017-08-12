<?php

namespace Gorilla;

use Gorilla\Contracts\EntityInterface;
use Gorilla\Contracts\MethodType;
use Gorilla\Entities\AccessToken;
use Gorilla\Exceptions\NonExistMethodException;
use Gorilla\Response\JsonResponse;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

/**
 * Class Client
 *
 * @package Gorilla
 */
class Client
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
     * @var null
     */
    private $accessToken = null;

    /**
     * @var array
     */
    private $config = [
        'base_uri' =>  'https://api.gorilladash.com',
    ];

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * Client constructor.
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
     * Set base uri
     *
     * @param $uri
     *
     * @return $this
     */
    public function setBaseUri($uri)
    {
         $this->config['base_uri'] = $uri;

         return $this;
    }

    /**
     * Set handler
     *
     * @param $handler
     *
     * @return $this
     */
    public function setHandler($handler)
    {
        $this->config['handler'] = $handler;

        return $this;
    }

    /**
     * Set access token
     *
     * @param $token
     *
     * @return $this
     */
    public function setAccessToken($token)
    {
        $this->accessToken = $token;

        return $this;
    }

    /**
     * Create client
     */
    public function createClient()
    {
        $this->client = new HttpClient($this->config);
    }

    /**
     * Request
     *
     * @param EntityInterface $entity
     *
     * @return JsonResponse|string
     */
    public function request(EntityInterface $entity)
    {
        if (!$this->client) {
            $this->createClient();
        }

        $options = $this->buildParameters($entity, []);

        $this->needAccess($entity, $options);

        try {
            $response = $this->client->request($entity->method(), $entity->endpoint(), $options);

            return new JsonResponse($response);
        } catch (RequestException $ex) {
            if ($ex->hasResponse()) {
                return Psr7\str($ex->getResponse());
            }

            return Psr7\str($ex->getRequest());
        }
    }

    /**
     * Build parameters
     *
     * @param EntityInterface $entity
     * @param                 $options
     *
     * @return array
     */
    private function buildParameters(EntityInterface $entity, $options)
    {
        if ($entity->method() === MethodType::GET) {
            $options['query'] = $entity->parameters();
        } else {
            $options['json'] = $entity->parameters();
        }

        return $options;
    }

    /**
     * Need access token
     *
     * @param EntityInterface $entity
     * @param                 $options
     *
     * @return void
     */
    private function needAccess(EntityInterface $entity, &$options)
    {
        if ($entity instanceof AccessToken) {
            return;
        }

        if (!$this->accessToken) {
            $accessTokenEntity = new AccessToken($this->id, $this->token);
            $response = $this->request($accessTokenEntity);
            $this->setAccessToken($response->json('access_token'));
        }

        $options['headers']['Authorization'] = "Bearer {$this->accessToken}";
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return JsonResponse|mixed|string
     * @throws \Gorilla\Exceptions\NonExistMethodException
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        }

        $entity = Factory::create($name, $arguments);
        if ($entity) {
            return $this->request($entity);
        }

        throw new NonExistMethodException("Sorry, we didn't find ${name}");
    }
}
