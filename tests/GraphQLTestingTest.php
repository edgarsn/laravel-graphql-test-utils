<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Tests;

use Newman\LaravelGraphQLTestUtils\Contracts\GraphQLTestingContract;
use Newman\LaravelGraphQLTestUtils\Drivers\RebingDriver;
use Newman\LaravelGraphQLTestUtils\Exceptions\DriverNotFoundException;
use Newman\LaravelGraphQLTestUtils\GraphQLTesting;
use Newman\LaravelGraphQLTestUtils\TestResponse;

class GraphQLTestingTest extends TestCase
{
    public function test_it_can_set_different_drivers(): void
    {
	GraphQLTesting::useDriver('custom');

	$this->assertEquals('custom', GraphQLTesting::getDriverName());

	GraphQLTesting::useRebing();

	$this->assertEquals('rebing', GraphQLTesting::getDriverName());
    }

    public function test_it_returns_driver_instance(): void
    {
	/** @var GraphQLTestingContract $testingInstance */
	$testingInstance = $this->app->make(GraphQLTestingContract::class);

	$this->assertInstanceOf(RebingDriver::class, $testingInstance->driver('rebing'));
    }

    public function test_it_throws_exception_when_non_existing_driver_is_passed(): void
    {
	/** @var GraphQLTestingContract $testingInstance */
	$testingInstance = $this->app->make(GraphQLTestingContract::class);

	$this->expectException(DriverNotFoundException::class);

	$testingInstance->driver('abc');
    }

    public function test_default_assertions(): void
    {
	GraphQLTesting::defaultAssertions(function (TestResponse $response) {

	});

	$this->assertIsCallable(GraphQLTesting::getDefaultAssertions());

	GraphQLTesting::defaultAssertions(null);

	$this->assertNull(GraphQLTesting::getDefaultAssertions());
    }
}
