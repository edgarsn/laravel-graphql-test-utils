<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Drivers;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Container\Container;
use Newman\LaravelGraphQLTestUtils\Contracts\DriverContract;

class RebingDriver implements DriverContract
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
        /** @var string|null $routePrefix */
        $routePrefix = $this->config->get('graphql.route.prefix');

        return $routePrefix;
    }

    public function getHttpMethodForSchema(string $schemaName): ?string
    {
        /** @var string|null $method */
        $method = $this->config->get('graphql.schemas.'.$schemaName.'.method.0');

        return $method;
    }
}
