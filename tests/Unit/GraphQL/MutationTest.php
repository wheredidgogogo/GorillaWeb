<?php

namespace Tests\Unit\GraphQL;

use Gorilla\GraphQL\Mutation;
use PHPUnit\Framework\TestCase;
use Tests\GraphQLAssert;

class MutationTest extends TestCase
{
    use GraphQLAssert;

    /** @test */
    public function add_field_to_mutation_builder()
    {
        // Arrange
        $query = new Mutation('update');

        // Act
        $query->fields([
            'name' => 'enquiry_name',
            'first_name' => 'first name',
            'email' => 'safe@example.com',
            'fields' => [
                [
                    'name' => 'first_field',
                    'value' => 'value',
                ],
                [
                    'name' => 'second_field',
                    'value' => 'value',
                ],
            ]
        ]);

        // Assert
        $this->assertGraphQLEqual(<<<EOF
    update (name: "enquiry_name", first_name: "first name", email: "safe@example.com",
            fields: [{ name: "first_field", value: "value" }, { name: "second_field", value: "value"}])
EOF
            , (string)$query);
    }
}
