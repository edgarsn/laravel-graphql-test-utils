<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils;

use Illuminate\Contracts\Foundation\Application;
use Newman\LaravelGraphQLTestUtils\Contracts\DriverContract;
use Newman\LaravelGraphQLTestUtils\Contracts\GraphQLTestingContract;
use Newman\LaravelGraphQLTestUtils\Exceptions\DriverNotFoundException;

class GraphQLTesting implements GraphQLTestingContract
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * Indicates which GraphQL package is being used in your.
     *
     * @var string
     */
    protected static $defaultDriver = 'rebing';

    /**
     * @var callable(TestResponse $response):void|null
     */
    protected static $defaultAssertions = null;

    /**
     * Response handler class.
     *
     * @var string
     */
    protected static $responseHandler = TestResponse::class;

    public function __construct(Application $app)
    {
	$this->app = $app;
    }

    /**
     * Use rebing package as default driver.
     *
     * @see https://github.com/rebing/graphql-laravel
     * @return void
     */
    public static function useRebing(): void
    {
	static::$defaultDriver = 'rebing';
    }

    /**
     * Use null as default driver.
     *
     * @return void
     */
    public static function useNullDriver(): void
    {
	static::$defaultDriver = 'null';
    }

    /**
     * Use custom default driver.
     *
     * @param string $name
     * @return void
     */
    public static function useDriver(string $name): void
    {
	static::$defaultDriver = $name;
    }

    /**
     * Get default driver name.
     *
     * @return string
     */
    public static function getDriverName(): string
    {
	return static::$defaultDriver;
    }

    /**
     * Build GraphQL response with different response class.
     *
     * @param string $class
     * @return void
     */
    public static function useCustomResponseHandler(string $class): void
    {
	static::$responseHandler = $class;
    }

    /**
     * Get response handler class.
     *
     * @return string
     */
    public static function getCustomResponseHandler(): string
    {
	return static::$responseHandler;
    }

    /**
     * @param callable(TestResponse $response):void|null $callable
     * @return void
     */
    public static function defaultAssertions(?callable $callable): void
    {
	static::$defaultAssertions = $callable;
    }

    /**
     * @return callable(TestResponse $response):void|null
     */
    public static function getDefaultAssertions(): ?callable
    {
	return static::$defaultAssertions;
    }

    /**
     * Get driver instance by name.
     *
     * @param string $name
     * @return DriverContract
     */
    public function driver(string $name): DriverContract
    {
	$abstract = 'laravel-graphql-utils-driver:' . $name;

	if ($this->app->has($abstract)) {
	    /** @var DriverContract $driver */
	    $driver = $this->app->make($abstract);

	    return $driver;
	}

	throw new DriverNotFoundException();
    }
}
