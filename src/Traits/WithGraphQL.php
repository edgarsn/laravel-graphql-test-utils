<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Traits;

use Illuminate\Contracts\Foundation\Application;
use Newman\LaravelGraphQLTestUtils\Contracts\GraphQLBuilderContract;

trait WithGraphQL
{
    public function graphql(): GraphQLBuilderContract
    {
	/** @var Application $app */
	$app = $this->app ?? app();

	$graphql = $app->make(GraphQLBuilderContract::class);

	return new $graphql($app);
    }
}
