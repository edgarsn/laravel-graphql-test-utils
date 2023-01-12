<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Contracts;

use Newman\LaravelGraphQLTestUtils\TestResponse;

interface GraphQLBuilderContract
{
    /**
     * @param string|null $query
     * @param array<string, mixed> $variables
     * @return TestResponse
     */
    public function call(?string $query = null, array $variables = []): TestResponse;
}
