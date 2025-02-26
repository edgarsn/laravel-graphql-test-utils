<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Tests\Drivers;

use Newman\LaravelGraphQLTestUtils\Drivers\NullDriver;
use Newman\LaravelGraphQLTestUtils\Tests\TestCase;

class NullDriverTest extends TestCase
{
    public function test_it_returns_expected_url_prefix(): void
    {
        $this->assertNull($this->getDriver()->getUrlPrefix());
    }

    public function test_it_returns_expected_http_method(): void
    {
        $this->assertNull($this->getDriver()->getHttpMethodForSchema('default'));
        $this->assertNull($this->getDriver()->getHttpMethodForSchema('postable'));
    }

    private function getDriver(): NullDriver
    {
        return $this->app->make('laravel-graphql-utils-driver:null');
    }
}
