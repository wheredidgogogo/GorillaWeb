<?php

namespace Tests\Unit\GraphQL;

use Gorilla\GraphQL\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
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
        $this->assertEquals('my_first_query  {  }', $collection->getCurrent()->__toString());
    }

    /** @test */
    public function add_filter_test()
    {
        // Arrange
        $collection = new Collection();
        $collection->query('my_first_query');

        // Act
        $collection->filters([
            'id' => '1',
            'name' => 'name',
        ]);

        // Assert
        $this->assertCount(1, $collection->getQueries());
        $this->assertEquals('query', $collection->getMethod());
        $this->assertEquals('my_first_query (id: "1",name: "name") {  }', $collection->getCurrent()->__toString());
    }

    /** @test */
    public function add_level_1_fields_test()
    {
        // Arrange
        $collection = new Collection();
        $collection->query('my_first_query');

        // Act
        $collection->fields([
            'first_field',
            'second_field',
        ]);

        // Assert
        $this->assertCount(1, $collection->getQueries());
        $this->assertEquals('query', $collection->getMethod());
        $this->assertEquals('my_first_query  { first_field,second_field }', $collection->getCurrent()->__toString());
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
            ]);

        // Assert
        $this->assertEquals(
            'query { my_first_query (name: "name") { first_field,second_field } }',
            (string)$collection
        );
    }
}
