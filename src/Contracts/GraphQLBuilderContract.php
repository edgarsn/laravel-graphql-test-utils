<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Contracts;

use Newman\LaravelGraphQLTestUtils\TestResponse;

interface GraphQLBuilderContract
{
    /**
     * @param  array<string, mixed>  $variables
     */
    public function call(?string $query = null, array $variables = []): TestResponse;
}
