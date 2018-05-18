<?php

namespace Tests\Unit\GraphQL;

use Gorilla\GraphQL\Query;
use PHPUnit\Framework\TestCase;
use Tests\GraphQLAssert;

class QueryTest extends TestCase
{
    use GraphQLAssert;

    /** @test */
    public function query_test()
    {
        // Act
        $query = new Query('first_query');
        // Assert
        $this->assertGraphQLEqual(<<<EOF
    first_query {
        
    }
EOF
,
            (string)$query
        );
    }

    /** @test */
    public function filter_test()
    {
        // Arrange
        $query = new Query('first_query');

        // Act
        $query->filters([
            'id' => '1',
            'name' => 'name',
        ]);

        // Assert
        $this->assertGraphQLEqual(<<<EOF
    first_query (id: "1",name: "name") {
        
    }
EOF
            ,
            (string)$query
        );
    }

    /** @test */
    public function filter_array_test()
    {
        // Arrange
        $query = new Query('first_query');

        // Act
        $query->filters([
            'id' => '1',
            'name' => [
                'homepage',
                'contact'
            ],
        ]);

        // Assert
        $this->assertGraphQLEqual(<<<EOF
    first_query (id: "1",name: ["homepage", "contact"]) {
        
    }
EOF
            ,
            (string)$query
        );
    }

    /** @test */
    public function field_test()
    {
        // Arrange
        $query = new Query('first_query');

        // Act
        $query->fields([
            'first_field',
            'second_field',
        ]);

        // Assert
        $this->assertGraphQLEqual(<<<EOF
    first_query {
        first_field,
        second_field,
    }
EOF
    ,
            (string)$query
            );
    }

    /** @test */
    public function deep_fields_test()
    {
        // Arrange
        $query = new Query('first_query');

        // Act
        $query->fields([
            'id',
            'name',
            'media' => [
                'name',
                'type',
                'media' => [
                    'id',
                    'name',
                    'banner',
                ],
            ],
        ]);

        // Assert
        $this->assertGraphQLEqual(<<<EOF
    first_query {
        id,
        name,
        media {
            name,
            type,
            media {
                id,
                name,
                banner,
            },
        },
    }
EOF
            ,
            (string)$query
        );
    }

    /** @test */
    public function deep_fields_with_filters_test()
    {
        // Arrange
        $query = new Query('first_query');

        $query->filters([
            'media' => [
                'name' => [
                    'Top banner',
                ],
                'limit' => 1,
            ],
            'media.media' => [
                'name' => [
                    'banner',
                    'thumbnail',
                ],
            ],
        ]);

        // Act
        $query->fields([
            'id',
            'name',
            'media' => [
                'name',
                'type',
                'media' => [
                    'id',
                    'name',
                    'banner',
                    'collection' => [
                        'name',
                    ],
                ],
            ],
        ]);

        // Assert
        $this->assertGraphQLEqual(<<<EOF
    first_query {
        id,
        name,
        media (name: ["Top banner"], limit: 1) {
            name,
            type,
            media (name: ["banner", "thumbnail"]) {
                id,
                name,
                banner,
                collection {
                    name,
                },
            },
        },
    }
EOF
            ,
            (string)$query
        );
    }
}
