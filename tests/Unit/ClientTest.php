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

    protected function setUp()
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(201),
        ]);
        $handler = HandlerStack::create($mock);

        $this->container = [];
        $history = Middleware::history($this->container);
        $handler->push($history);

        $this->client = new Client('fakeId', 'fakeToken');
        $this->client->setHandler($handler);
    }

    /** @test */
    public function get_parameters_builder()
    {
        // Arrange
        $this->client->setAccessToken('fake access token');

        // Act
        $this->client->request(new getStub());

        /** @var Request $request */
        $request = $this->container[0]['request'];

        // Assert
        $this->assertEquals(MethodType::GET, $request->getMethod());
        $this->assertEquals('first=1&second=1', $request->getUri()->getQuery());
    }

    /** @test */
    public function post_parameters_builder()
    {
        // Arrange
        $this->client->setAccessToken('fake access token');

        // Act
        $this->client->request(new postStub());
        /** @var Request $request */
        $request = $this->container[0]['request'];

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
        $this->client->setAccessToken('fake access token');

        // Act
        $this->client->request(new postStub());
        /** @var Request $request */
        $request = $this->container[0]['request'];

        // Assert
        $this->assertEquals('Bearer fake access token', $request->getHeader('Authorization')[0]);
    }

    /** @test */
    public function auto_get_access_token_if_token_is_missing()
    {
        // Arrange
        $mock = new MockHandler([
            new Response(201, [], json_encode(['access_token' => 'FooBar'])),
            new Response(201),
        ]);
        $handler = HandlerStack::create($mock);
        $container = [];
        $history = Middleware::history($container);
        $handler->push($history);

        $this->client->setHandler($handler);

        // Act
        $this->client->request(new postStub());
        $request = $container[1]['request'];

        // Assert
        $this->assertCount(2, $container);
        $this->assertEquals('Bearer FooBar', $request->getHeader('Authorization')[0]);
    }

    /** @test */
    public function change_base_uri()
    {
        // Arrange
        $this->client->setAccessToken('Fake token');
        $this->client->setBaseUri('https://www.google.com');

        // Act
        $this->client->request(new postStub());
        /** @var Request $request */
        $request = $this->container[0]['request'];
        
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
}