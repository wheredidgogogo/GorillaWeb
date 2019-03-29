<?php

namespace Tests\Unit;

use Gorilla\Client;
use Gorilla\Contracts\EntityInterface;
use Gorilla\Contracts\MethodType;
use Gorilla\Contracts\RequestInterface;
use Gorilla\Entities\Menu;
use Gorilla\Exceptions\NonExistMethodException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $container;

    protected function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(201, [], json_encode([
                'access_token' => 'FooBar',
                'expires_in' => 100000,
            ])),
            new Response(201),
        ]);
        $handler = HandlerStack::create($mock);

        $this->container = [];
        $history = Middleware::history($this->container);
        $handler->push($history);

        $this->client = new Client('fakeId', 'fakeToken');
        $this->client->setHandler($handler);
        $this->client->setCachePath(dirname(dirname(__DIR__)).'/cache');
    }

    /** @test */
    public function get_parameters_builder()
    {
        // Act
        $this->client->request(new getStub());

        /** @var Request $request */
        $request = end($this->container)['request'];

        // Assert
        $this->assertEquals(MethodType::GET, $request->getMethod());
        $this->assertEquals('first=1&second=1', $request->getUri()->getQuery());
    }

    /** @test */
    public function post_parameters_builder()
    {
        // Arrange

        // Act
        $this->client->request(new postStub());
        /** @var Request $request */
        $request = end($this->container)['request'];

        // Assert
        $this->assertEquals(MethodType::POST, $request->getMethod());
        $this->assertArraySubset([
            'first' => 1,
            'second' => 1,
        ], json_decode($request->getBody()->__toString(), true));
    }

    /** @test */
    public function access_token_builder()
    {
        // Arrange

        // Act
        $this->client->request(new postStub());
        /** @var Request $request */
        $request = end($this->container)['request'];

        // Assert
        $this->assertEquals('Bearer FooBar', $request->getHeader('Authorization')[0]);
    }

    /** @test */
    public function change_base_uri()
    {
        // Arrange
        $this->client->setBaseUri('https://www.google.com');

        // Act
        $this->client->request(new postStub());
        /** @var Request $request */
        $request = end($this->container)['request'];

        // Assert
        $this->assertEquals('https://www.google.com', $request->getUri()->__toString());
    }

    /** @test */
    public function call_entity()
    {
        // Assert
        $this->assertInstanceOf(Menu::class, $this->client->menus());
    }

    /** @test */
    public function no_exist_method()
    {
        $this->expectException(NonExistMethodException::class);

        $this->client->foobar();
    }

    /** @test */
    public function enable_cache_use_default_cache_second()
    {
        // Arrange

        // Act
        $this->client->setDefaultCacheSecond(600);
        $this->client->cache();

        // Assert
        $this->assertTrue($this->client->isCacheEnabled());
        $this->assertEquals(600, $this->client->getCacheSeconds());
    }

    /** @test */
    public function enable_cache()
    {
        // Arrange

        // Act
        $this->client->cache(700);

        // Assert
        $this->assertTrue($this->client->isCacheEnabled());
        $this->assertEquals(700, $this->client->getCacheSeconds());
    }
}

class getStub implements EntityInterface
{

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
        return [
            'first' => 1,
            'second' => 1,
        ];
    }

    /**
     * Endpoint url
     *
     * @return string
     */
    public function endpoint()
    {
        return '/';
    }

    /**
     * Set Request
     *
     * @param RequestInterface $request
     *
     * @return mixed
     */
    public function setRequest(RequestInterface $request)
    {
        // TODO: Implement setRequest() method.
    }

    /**
     * Get last update at
     *
     * @return string|null
     */
    public function getLastUpdatedAt()
    {
        // TODO: Implement getLastUpdatedAt() method.
    }

    /**
     * Set last update at
     *
     * @param ?string $lastUpdatedAt
     *
     * @return \Gorilla\Contracts\EntityAbstract
     */
    public function setLastUpdatedAt($lastUpdatedAt)
    {
        // TODO: Implement setLastUpdatedAt() method.
    }
}

class postStub implements EntityInterface
{

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
            'first' => 1,
            'second' => 1,
        ];
    }

    /**
     * Endpoint url
     *
     * @return string
     */
    public function endpoint()
    {
        return '';
    }

    /**
     * Set Request
     *
     * @param RequestInterface $request
     *
     * @return mixed
     */
    public function setRequest(RequestInterface $request)
    {
        // TODO: Implement setRequest() method.
    }

    /**
     * Get last update at
     *
     * @return string|null
     */
    public function getLastUpdatedAt()
    {
        // TODO: Implement getLastUpdatedAt() method.
    }

    /**
     * Set last update at
     *
     * @param ?string $lastUpdatedAt
     *
     * @return \Gorilla\Contracts\EntityAbstract
     */
    public function setLastUpdatedAt($lastUpdatedAt)
    {
        // TODO: Implement setLastUpdatedAt() method.
    }
}
