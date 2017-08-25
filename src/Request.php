<?php

namespace Gorilla;

use Gorilla\Contracts\EntityInterface;
use Gorilla\Contracts\MethodType;
use Gorilla\Contracts\RequestInterface;
use Gorilla\Entities\AccessToken;
use Gorilla\Exceptions\ResponseException;
use Gorilla\Response\JsonResponse;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7;

class Request implements RequestInterface
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
     * @throws \Gorilla\Exceptions\ResponseException
     * @throws \GuzzleHttp\Exception\RequestException
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
                throw new ResponseException(Psr7\str($ex->getResponse()));
            }

            throw new ResponseException(Psr7\str($ex->getRequest()));
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

        $options['headers']['X-Requested-With'] = 'XMLHttpRequest';

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
}