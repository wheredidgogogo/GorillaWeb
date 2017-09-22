<?php

namespace Tests\Feature;

use DateTimeInterface;
use Gorilla\Entities\GraphQL;
use Gorilla\GraphQL\Collection;
use Gorilla\GraphQL\Query;
use Gorilla\Request;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use phpFastCache\CacheManager;
use phpFastCache\Core\Pool\ExtendedCacheItemPoolInterface;
use PHPUnit\Framework\TestCase;

class GraphQLCacheTest extends TestCase
{
    /**
     * @var ExtendedCacheItemPoolInterface
     */
    private static $cache;

    public function setUp()
    {
        if (!self::$cache) {
            CacheManager::setDefaultConfig([
                'path' => dirname(dirname(__DIR__)).'/cache',
                'ignoreSymfonyNotice' => true,
            ]);
            self::$cache = CacheManager::getInstance('files');
        }
    }

    /** @test */
    public function get_data_from_cached()
    {
        self::$cache->clear();
        // Arrange
        $collection = new Collection();
        $collection->query('my_first_query')
            ->filters([
                'name' => 'name',
            ])
            ->fields([
                'first_field',
                'second_field',
                'media' => [
                    'id',
                    'name',
                ],
            ])
            ->query('second_query')
            ->fields([
                'first_field',
                'second_field',
                'media' => [
                    'id',
                    'name',
                ],
            ]);

        $cloneQueries = $collection->getQueries();

        $graphQL = new GraphQL($collection);
        $graphQL->cache();

        $this->buildCache($collection->getQueries(), $graphQL->getExpires());

        // Act
        $graphQL->getCached();
        $response = collect($graphQL->getCacheData());

        // Assert
        $this->assertCount(0 , $collection->getQueries());
        foreach ($cloneQueries as $query) {
            $this->assertEquals($query->cacheKey(), $response->get($query->getName()));
        }
    }

    /** @test */
    public function get_cache_but_one_query_was_expired()
    {
        self::$cache->clear();
        // Arrange
        $collection = new Collection();
        $collection->query('my_first_query')
            ->filters([
                'name' => 'name',
            ])
            ->fields([
                'first_field',
                'second_field',
                'media' => [
                    'id',
                    'name',
                ],
            ])
            ->query('second_query')
            ->fields([
                'first_field',
                'second_field',
                'media' => [
                    'id',
                    'name',
                ],
                'bar',
                'foo',
            ]);

        $graphQL = new GraphQL($collection);
        $graphQL->cache();

        $this->buildCache(collect([$collection->getQueries()->get(0)]), 6000);
        $this->buildCache(collect([$collection->getQueries()->get(1)]), (new \DateTime('now'))->modify('-1 week'));

        $graphQL->getCached();

        // Assert
        $this->assertCount(1, $graphQL->getCacheData());
        $this->assertCount(1, $collection->getQueries());
    }

    /** @test */
    public function merge_cache_and_response_data()
    {
        self::$cache->clear();
        // Arrange
        $collection = new Collection();
        $collection->query('my_first_query')
            ->filters([
                'name' => 'name',
            ])
            ->fields([
                'first_field',
                'second_field',
                'media' => [
                    'id',
                    'name',
                ],
            ])
            ->query('second_query')
            ->fields([
                'first_field',
                'second_field',
                'media' => [
                    'id',
                    'name',
                ],
                'bar',
                'foo',
            ]);

        $graphQL = new GraphQL($collection);
        $graphQL->cache();
        /** @var Query[] $cloneQueries */
        $cloneQueries = $collection->getQueries();

        $this->buildCache(collect([$collection->getQueries()->get(0)]), 6000);
        $this->buildCache(collect([$collection->getQueries()->get(1)]), (new \DateTime('now'))->modify('-1 week'));

        $request = new Request('id', 'token');
        $request->setHandler($this->getMockHandler([
            'second_query' => 'response',
        ]));

        // Act
        $response = $request->request($graphQL);

        // Assert
        $this->assertArraySubset([
            $cloneQueries->get(0)->getName() => $cloneQueries->get(0)->cacheKey(),
            $cloneQueries->get(1)->getName() => 'response',
        ], $response->json());
    }

    /** @test */
    public function get_response_without_cache()
    {
        self::$cache->clear();
        // Arrange
        $collection = new Collection();
        $collection->query('my_first_query')
            ->filters([
                'name' => 'name',
            ])
            ->fields([
                'first_field',
                'second_field',
                'media' => [
                    'id',
                    'name',
                ],
            ])
            ->query('second_query')
            ->fields([
                'first_field',
                'second_field',
                'media' => [
                    'id',
                    'name',
                ],
                'bar',
                'foo',
            ]);

        $graphQL = new GraphQL($collection);
        $graphQL->cache();
        /** @var Query[] $cloneQueries */
        $cloneQueries = $collection->getQueries();

        $request = new Request('id', 'token');
        $request->setHandler($this->getMockHandler([
            'my_first_query' => 'response',
            'second_query' => 'response',
        ]));

        // Act
        $response = $request->request($graphQL);

        // Assert
        $this->assertArraySubset([
            $cloneQueries->get(0)->getName() => 'response',
            $cloneQueries->get(1)->getName() => 'response',
        ], $response->json());
    }

    /** @test */
    public function get_response_all_in_cache()
    {
        self::$cache->clear();
        // Arrange
        $collection = new Collection();
        $collection->query('my_first_query')
            ->filters([
                'name' => 'name',
            ])
            ->fields([
                'first_field',
                'second_field',
                'media' => [
                    'id',
                    'name',
                ],
            ])
            ->query('second_query')
            ->fields([
                'first_field',
                'second_field',
                'media' => [
                    'id',
                    'name',
                ],
                'bar',
                'foo',
            ]);

        $graphQL = new GraphQL($collection);
        $graphQL->cache();
        /** @var Query[] $cloneQueries */
        $cloneQueries = $collection->getQueries();

        $this->buildCache(collect([$collection->getQueries()->get(0)]), 6000);
        $this->buildCache(collect([$collection->getQueries()->get(1)]), 6000);

        $request = new Request('id', 'token');
        $request->setHandler($this->getMockHandler([
            'second_query' => 'response',
        ]));

        // Act
        $response = $request->request($graphQL);

        // Assert
        $this->assertArraySubset([
            $cloneQueries->get(0)->getName() => $cloneQueries->get(0)->cacheKey(),
            $cloneQueries->get(1)->getName() => $cloneQueries->get(1)->cacheKey(),
        ], $response->json());
    }

    /** @test */
    public function get_response_and_save_cache()
    {
        self::$cache->clear();
        // Arrange
        $collection = new Collection();
        $collection->query('my_first_query')
            ->filters([
                'name' => 'name',
            ])
            ->fields([
                'first_field',
                'second_field',
                'media' => [
                    'id',
                    'name',
                ],
            ])
            ->query('second_query')
            ->fields([
                'first_field',
                'second_field',
                'media' => [
                    'id',
                    'name',
                ],
                'bar',
                'foo',
            ]);

        $graphQL = new GraphQL($collection);
        $graphQL->cache();

        $request = new Request('id', 'token');
        $request->setHandler($this->getMockHandler([
            'my_first_query' => 'first_query',
            'second_query' => 'response',
        ]));

        $request->request($graphQL);

        foreach ($collection->getQueries() as $query) {
            $this->assertTrue(self::$cache->hasItem($query->cacheKey()));
        }
    }

    private function buildCache(\Illuminate\Support\Collection $array, $expires)
    {
        $array->each(function (Query $query) use ($expires) {
            $cache = self::$cache->getItem($query->cacheKey())->set($query->cacheKey());

            if ($expires instanceof DateTimeInterface) {
                $cache->expiresAt($expires);
            } else {
                $cache->expiresAfter($expires);
            }

            self::$cache->save($cache);
        });
    }

    private function getMockHandler($response)
    {
        $mock = new MockHandler([
            new Response(201, [], json_encode([
                'access_token' => 'FooBar',
                'expires_in' => 100000,
            ])),
            new Response(201, [], json_encode($response)),
        ]);
        return HandlerStack::create($mock);
    }
}
