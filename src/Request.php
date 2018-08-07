<?php

namespace Gorilla;

use Gorilla\Contracts\CanCached;
use Gorilla\Contracts\EntityInterface;
use Gorilla\Contracts\MethodType;
use Gorilla\Contracts\RequestInterface;
use Gorilla\Entities\AccessToken as AccessTokenEntity;
use Gorilla\Entities\LastUpdatedAt;
use Gorilla\Exceptions\ResponseException;
use Gorilla\Response\JsonResponse;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use Log;
use RuntimeException;
use Tightenco\Collect\Support\Arr;

/**
 * Class Request
 *
 * @package Gorilla
 */
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
     * @var AccessToken
     */
    private $accessToken;

    /**
     * @var string
     */
    public static $lastUpdatedAt;

    /**
     * @var array
     */
    private $config = [
        'base_uri' => 'https://api.gorilladash.com',
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
     *
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
     * @throws \RuntimeException
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidConfigurationException
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     * @throws \phpFastCache\Exceptions\phpFastCacheDriverCheckException
     * @throws \InvalidArgumentException
     * @throws \Gorilla\Exceptions\ResponseException
     * @throws \GuzzleHttp\Exception\RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function request(EntityInterface $entity)
    {
        if (!$this->client) {
            $this->createClient();
        }

        $options = $this->buildParameters($entity, []);
        $this->needAccess($entity, $options);

        if ($entity instanceof CanCached) {
            $entity->setLastUpdatedAt(self::$lastUpdatedAt);
            $entity->getCached();
            if ($entity->allInCached()) {
                return new JsonResponse($entity->merge([]));
            }
        }
        try {
            $response = $this->client->request($entity->method(), $entity->endpoint(), $options);
            $data = json_decode($response->getBody()->getContents(), true);
            if ($entity instanceof CanCached) {
                $entity->saveCache(Arr::get($data, 'data', []));
                $data = $entity->merge($data);
            }

            return new JsonResponse($data);
        } catch (RequestException $ex) {
            if ($ex->hasResponse()) {
                Log::error(json_encode($options));
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
     * @throws \GuzzleHttp\Exception\RequestException
     * @throws \Gorilla\Exceptions\ResponseException
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidConfigurationException
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     * @throws \phpFastCache\Exceptions\phpFastCacheDriverCheckException
     * @throws \InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function needAccess(EntityInterface $entity, &$options)
    {
        $accessToken = new AccessToken();

        if ($entity instanceof AccessTokenEntity) {
            return;
        }

        try {
            $accessToken->loadAccessTokenFromCached();
            $this->setAccessToken($accessToken);

            if ($this->accessToken->hasExpired()) {
                throw new RuntimeException('Token was expired');
            }
        } catch (RuntimeException $ex) {
            $accessTokenEntity = new AccessTokenEntity($this->id, $this->token);
            $response = $this->request($accessTokenEntity);
            $token = $response->json();
            $accessToken->setup($token['access_token'], $token['expires_in']);
            $this->setAccessToken($accessToken);
        }
        $options['headers']['Authorization'] = "Bearer {$this->accessToken->getAccessToken()}";
        if (!$entity instanceof LastUpdatedAt) {
            $this->getLastUpdatedAt();
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \phpFastCache\Exceptions\phpFastCacheDriverCheckException
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidConfigurationException
     */
    private function getLastUpdatedAt()
    {
        if (self::$lastUpdatedAt) {
            return;
        }
        $query = new \Gorilla\GraphQL\Collection();
        $query->query('lastUpdatedAt');
        $graphQL = new LastUpdatedAt($query);
        $response = $this->request($graphQL);
        self::$lastUpdatedAt = data_get($response->json('data'), 'lastUpdatedAt');
    }
}
