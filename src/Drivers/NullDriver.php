<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Drivers;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Container\Container;
use Newman\LaravelGraphQLTestUtils\Contracts\DriverContract;

class NullDriver implements DriverContract
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var ConfigContract
     */
    protected $config;

    public function __construct(Container $app, ConfigContract $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    public function getUrlPrefix(): ?string
    {
        return null;
    }

    public function getHttpMethodForSchema(string $schemaName): ?string
    {
        return null;
    }
}
