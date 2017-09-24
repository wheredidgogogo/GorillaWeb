<?php

namespace Tests\Unit\GraphQL;

use Gorilla\GraphQL\Filter;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    /** @test */
    public function is_base_filter_test()
    {
        // Arrange
        $filter = new Filter('name', [
            'foo',
            'bar',
        ]);
        // Act

        // Assert
        $this->assertFalse($filter->isSubFilter());
    }

    /** @test */
    public function is_sub_filter_test()
    {
        // Arrange
        $filter = new Filter('content', [
            'name' => [
                'foo',
                'bar',
            ],
        ]);
        // Act

        // Assert
        $this->assertTrue($filter->isSubFilter());
    }
}
