<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Tests;

use Newman\LaravelGraphQLTestUtils\GraphQLBuilder;
use Newman\LaravelGraphQLTestUtils\Tests\Support\ClassWithGraphQLTrait;

class WithGraphQLTraitTest extends TestCase
{
    public function test(): void
    {
	$testClass = new ClassWithGraphQLTrait($this->app);

	$this->assertInstanceOf(GraphQLBuilder::class, $testClass->graphql());
    }
}
