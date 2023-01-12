<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Tests;

use Newman\LaravelGraphQLTestUtils\GraphQLRequest;
use Newman\LaravelGraphQLTestUtils\GraphQLTesting;
use Newman\LaravelGraphQLTestUtils\TestResponse;
use Newman\LaravelGraphQLTestUtils\Tests\Support\CustomTestResponse;

class GraphQLRequestTest extends TestCase
{
    public function test_it_returns_default_response_handler(): void
    {
	$request = new GraphQLRequest($this->app);

	$response = $request->call('GET', '/simple');

	$this->assertInstanceOf(TestResponse::class, $response);
    }

    public function test_custom_response_handler(): void
    {
	GraphQLTesting::useCustomResponseHandler(CustomTestResponse::class);

	$request = new GraphQLRequest($this->app);

	$response = $request->call('GET', '/simple');

	$this->assertInstanceOf(CustomTestResponse::class, $response);

	GraphQLTesting::useCustomResponseHandler(TestResponse::class);
    }
}
