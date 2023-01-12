<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Contracts;

interface DriverContract
{
    public function getUrlPrefix(): ?string;

    public function getHttpMethodForSchema(string $schemaName): ?string;
}
