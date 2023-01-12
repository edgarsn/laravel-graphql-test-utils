<?php

namespace Newman\LaravelGraphQLTestUtils\Tests\Support;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Container\Container;
use Newman\LaravelGraphQLTestUtils\Contracts\DriverContract;

class LoremDriver implements DriverContract
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
	return 'lorem-graphql';
    }

    public function getHttpMethodForSchema(string $schemaName): ?string
    {
	return 'GET';
    }
}
