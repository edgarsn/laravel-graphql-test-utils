<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Tests;

use Newman\LaravelGraphQLTestUtils\GraphQLRequest;
use Newman\LaravelGraphQLTestUtils\TestResponse;

class TestResponseTest extends TestCase
{
    public function test_it_can_set_and_get_query(): void
    {
	$request = new GraphQLRequest($this->app);
	/** @var TestResponse $response */
	$response = $request->call('GET', '/success-response');

	$response->setQuery('query { lorem }');

	$this->assertEquals('query { lorem }', $response->getQuery());
    }

    public function test_it_can_set_and_get_variables(): void
    {
	$request = new GraphQLRequest($this->app);
	/** @var TestResponse $response */
	$response = $request->call('GET', '/success-response');

	$response->setVariables(['a' => 1, 'b' => 2]);

	$this->assertEquals(['a' => 1, 'b' => 2], $response->getVariables());
	$this->assertEquals(2, $response->getVariable('b'));
    }

    public function test_getGraphqlErrors(): void
    {
	$request = new GraphQLRequest($this->app);
	/** @var TestResponse $response */
	$response = $request->call('GET', '/errors-response');

	$this->assertEquals([
	    [
		'message' => 'Failed',
		'extensions' => [
		    'category' => 'graphql',
		],
	    ]
	], $response->getGraphQLErrors());

	/** @var TestResponse $response */
	$response = $request->call('GET', '/success-response');

	$this->assertNull($response->getGraphQLErrors());
    }

    public function test_hasGraphQLErrors(): void
    {
	$request = new GraphQLRequest($this->app);
	/** @var TestResponse $response */
	$response = $request->call('GET', '/errors-response');

	$this->assertTrue($response->hasGraphQLErrors());

	/** @var TestResponse $response */
	$response = $request->call('GET', '/success-response');

	$this->assertFalse($response->hasGraphQLErrors());
    }

    public function test_getGraphQLValidationMessages(): void
    {
	$request = new GraphQLRequest($this->app);
	/** @var TestResponse $response */
	$response = $request->call('GET', '/validation-errors-response');

	$this->assertEquals([
	    'name' => ['This field is required.', 'It must be string.'],
	    'email' => ['It must be valid e-mail.'],
	], $response->getGraphQLValidationMessages());

	/** @var TestResponse $response */
	$response = $request->call('GET', '/errors-response');

	$this->assertEquals([], $response->getGraphQLValidationMessages());
    }

    public function test_getValidationFieldMessages(): void
    {
	$request = new GraphQLRequest($this->app);
	/** @var TestResponse $response */
	$response = $request->call('GET', '/validation-errors-response');

	$this->assertEquals(['This field is required.', 'It must be string.'], $response->getValidationFieldMessages('name'));
	$this->assertEquals([], $response->getValidationFieldMessages('abc'));

	/** @var TestResponse $response */
	$response = $request->call('GET', '/errors-response');

	$this->assertEquals([], $response->getValidationFieldMessages('name'));
    }

    public function test_getValidationFieldFirstMessage(): void
    {
	$request = new GraphQLRequest($this->app);
	/** @var TestResponse $response */
	$response = $request->call('GET', '/validation-errors-response');

	$this->assertEquals('This field is required.', $response->getValidationFieldFirstMessage('name'));
	$this->assertNull($response->getValidationFieldFirstMessage('abc'));

	/** @var TestResponse $response */
	$response = $request->call('GET', '/errors-response');

	$this->assertNull($response->getValidationFieldFirstMessage('name'));
    }
}
