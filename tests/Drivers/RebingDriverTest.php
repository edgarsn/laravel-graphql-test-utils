<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Tests\Drivers;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Newman\LaravelGraphQLTestUtils\Drivers\RebingDriver;
use Newman\LaravelGraphQLTestUtils\Tests\TestCase;

class RebingDriverTest extends TestCase
{
    public function test_it_returns_expected_url_prefix(): void
    {
        /** @var ConfigContract $config */
        $config = $this->app->make(ConfigContract::class);

        $config->set('graphql', [
            'route' => [
                'prefix' => 'my-graphql',
                'controller' => '',
                'middleware' => [],
                'group_attributes' => [],
            ],
        ]);

        $this->assertEquals('my-graphql', $this->getDriver()->getUrlPrefix());
    }

    public function test_it_returns_expected_http_method(): void
    {
        /** @var ConfigContract $config */
        $config = $this->app->make(ConfigContract::class);

        $config->set('graphql', [
            'schemas' => [
                'default' => [
                    'query' => [],
                    'mutation' => [],
                    'middleware' => [],
                    'method' => ['GET'],
                    'execution_middleware' => null,
                ],
                'postable' => [
                    'query' => [],
                    'mutation' => [],
                    'middleware' => [],
                    'method' => ['POST'],
                    'execution_middleware' => null,
                ],
            ],
        ]);

        $this->assertEquals('GET', $this->getDriver()->getHttpMethodForSchema('default'));
        $this->assertEquals('POST', $this->getDriver()->getHttpMethodForSchema('postable'));
    }

    private function getDriver(): RebingDriver
    {
        return $this->app->make('laravel-graphql-utils-driver:rebing');
    }
}
