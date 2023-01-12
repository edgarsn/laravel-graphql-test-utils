<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Tests;

use Newman\LaravelGraphQLTestUtils\Exceptions\GraphQLQueryNotProvidedException;
use Newman\LaravelGraphQLTestUtils\GraphQLBuilder;
use Newman\LaravelGraphQLTestUtils\GraphQLTesting;
use Newman\LaravelGraphQLTestUtils\TestResponse;

class GraphQLBuilderTest extends TestCase
{
    public function test_merge_Variables(): void
    {
	$builder = new GraphQLBuilder($this->app);

	$response = $builder->setVariables(['a' => 1, 'b' => 2])
	    ->mergeVariables(['c' => 3, 'b' => 4])
	    ->httpMethod('get')
	    ->call('query { cars }');

	$this->assertEquals(['a' => 1, 'b' => 4, 'c' => 3], $response->getVariables());
	$this->assertEquals([
	    'cars' => [
		['title' => 'Skoda'],
		['title' => 'Audi'],
	    ],
	], $response->json('data'));
    }

    public function test_it_throws_exception_when_query_is_not_provided(): void
    {
	$builder = new GraphQLBuilder($this->app);

	$this->expectException(GraphQLQueryNotProvidedException::class);

	$builder->call();
    }

    public function test_schema(): void
    {
	$builder = new GraphQLBuilder($this->app);

	$response = $builder->schema('postable')->call('query { cars }');

	$this->assertEquals(['success' => true], $response->json('data'));
    }

    public function test_driver(): void
    {
	$builder = new GraphQLBuilder($this->app);

	$response = $builder->driver('lorem')->call('query { brands }');

	$this->assertEquals([
	    'brands' => [
		['title' => 'Snickers'],
		['title' => 'Coca-Cola'],
	    ]
	], $response->json('data'));
    }

    public function test_default_assertions(): void
    {
	static $is_callable_called = false;

	GraphQLTesting::defaultAssertions(function (TestResponse $response) use (&$is_callable_called) {
	    $is_callable_called = true;
	});

	$builder = new GraphQLBuilder($this->app);

	$builder->httpMethod('get')
	    ->call('query { cars }');

	$this->assertTrue($is_callable_called);

	$is_callable_called = false;

	$builder->httpMethod('get')
	    ->withoutDefaultAssertions()
	    ->call('query { cars }');

	$this->assertFalse($is_callable_called);
    }
}
