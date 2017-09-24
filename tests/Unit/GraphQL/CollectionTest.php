<?php

namespace Tests\Unit\GraphQL;

use Gorilla\GraphQL\Collection;
use PHPUnit\Framework\TestCase;
use Tests\GraphQLAssert;

class CollectionTest extends TestCase
{
    use GraphQLAssert;

    /** @test */
    public function add_query_test()
    {
        // Arrange
        $collection = new Collection();

        // Act
        $collection->query('my_first_query');

        // Assert
        $this->assertCount(1, $collection->getQueries());
        $this->assertEquals('query', $collection->getMethod());
    }

    /** @test */
    public function reset_test()
    {
        // Arrange
        $collection = new Collection();
        $collection->query('my_first_query');
        $collection->fields([
            'first_field',
            'second_field',
        ]);

        // Act
        $collection->reset();

        // Assert
        $this->assertCount(0, $collection->getQueries());
        $this->assertNull($collection->getCurrent());
    }

    /** @test */
    public function get_query_string()
    {
        // Arrange
        $collection = new Collection();

        // Act
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
            ]);

        // Assert
        $this->assertGraphQLEqual(<<<EOF
    query {
        my_first_query (name: "name") {
            first_field,
            second_field,
            media {
                id,
                name,
            },
        }
    }
EOF
,
            (string)$collection
        );
    }

    /** @test */
    public function multiple_queries_test()
    {
        // Arrange
        $collection = new Collection();

        // Act
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
            ]);

        $collection->query('second_query')
            ->fields([
                'first_field',
                'second_field',
                'media' => [
                    'id',
                    'name',
                ],
            ]);

        // Assert
        $this->assertGraphQLEqual(<<<EOF
    query {
        my_first_query (name: "name") {
            first_field,
            second_field,
            media {
                id,
                name,
            },
        }
        
        second_query {
            first_field,
            second_field,
            media {
                id,
                name,
            },
        }
    }
EOF
            ,
            (string)$collection
        );
    }

    /** @test */
    public function mutation_test()
    {
        // Arrange
        $collection = new Collection();

        // Act
        $collection->mutation('submit_enquiry')
            ->fields([
                'name' => 'enquiry_form_name',
                'first_name' => 'Foo',
                'email' => 'safe@example.com',
                'mobile' => '0000000000',
                'ip' => '127.0.0.1',
                'tribes' => ['tribe_slug'],
                'fields' => [
                    [
                        'name' => 'first_field',
                        'value' => 'first_value',
                    ],
                    [
                        'name' => 'second_field',
                        'value' => 'second_value',
                    ],
                ],
            ]);

        // Assert
        $this->assertGraphQLEqual(<<<EOF
    mutation {
        submit_enquiry (name: "enquiry_form_name", first_name: "Foo", email: "safe@example.com",
            mobile: "0000000000", ip: "127.0.0.1", tribes: ["tribe_slug"],
            fields: [{ name: "first_field", value: "first_value" }, { name: "second_field", value: "second_value" }]) 
    }
EOF
        ,
            (string)$collection
        );
    }
}
