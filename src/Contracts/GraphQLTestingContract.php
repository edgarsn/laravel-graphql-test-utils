<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Contracts;

interface GraphQLTestingContract
{
    public function driver(string $name): DriverContract;
}
