<?php

namespace Tests;

trait GraphQLAssert
{
    public function assertGraphQLEqual($expected, $actual)
    {
        $pattern = '/\s+/';

        $this->assertEquals(
            preg_replace($pattern, '', $expected),
            preg_replace($pattern, '', $actual)
        );
    }
}