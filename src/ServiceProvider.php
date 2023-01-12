<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils;

use Newman\LaravelGraphQLTestUtils\Contracts\GraphQLBuilderContract;
use Newman\LaravelGraphQLTestUtils\Contracts\GraphQLTestingContract;
use Newman\LaravelGraphQLTestUtils\Drivers\NullDriver;
use Newman\LaravelGraphQLTestUtils\Drivers\RebingDriver;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
	$this->app->bind(GraphQLBuilderContract::class, GraphQLBuilder::class);
	$this->app->bind(GraphQLTestingContract::class, GraphQLTesting::class);

	// drivers
	$this->app->bind('laravel-graphql-utils-driver:rebing', RebingDriver::class);
	$this->app->bind('laravel-graphql-utils-driver:null', NullDriver::class);
    }
}
