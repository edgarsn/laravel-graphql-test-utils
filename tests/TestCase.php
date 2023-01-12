<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Tests;

use Newman\LaravelGraphQLTestUtils\ServiceProvider;
use Newman\LaravelGraphQLTestUtils\Tests\Support\LoremDriver;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Define routes setup.
     *
     * @param \Illuminate\Routing\Router $router
     * @return void
     */
    protected function defineRoutes($router)
    {
	$router->get('/graphql', function () {
	    return response()->json([
		'data' => [
		    'cars' => [
			['title' => 'Skoda'],
			['title' => 'Audi'],
		    ],
		],
	    ]);
	});

	$router->get('/lorem-graphql', function () {
	    return response()->json([
		'data' => [
		    'brands' => [
			['title' => 'Snickers'],
			['title' => 'Coca-Cola'],
		    ],
		],
	    ]);
	});

	$router->post('/graphql/postable', function () {
	    return response()->json([
		'data' => [
		    'success' => true,
		],
	    ]);
	});

	$router->get('/success-response', function () {
	    return response()->json([
		'data' => [
		    'cars' => [
			[
			    'title' => 'Skoda',
			],
			[
			    'title' => 'Audi',
			],
		    ],
		],
	    ]);
	});

	$router->get('/errors-response', function () {
	    return response()->json([
		'data' => null,
		'errors' => [
		    [
			'message' => 'Failed',
			'extensions' => [
			    'category' => 'graphql',
			],
		    ]
		],
	    ]);
	});

	$router->get('/validation-errors-response', function () {
	    return response()->json([
		'data' => null,
		'errors' => [
		    [
			'message' => 'Failed',
			'extensions' => [
			    'category' => 'graphql',
			    'validation' => [
				'name' => ['This field is required.', 'It must be string.'],
				'email' => ['It must be valid e-mail.'],
			    ],
			],
		    ]
		],
	    ]);
	});
    }

    protected function setUp(): void
    {
	parent::setUp();

	$this->app->bind('laravel-graphql-utils-driver:lorem', LoremDriver::class);
    }

    protected function getPackageProviders($app): array
    {
	return [
	    ServiceProvider::class,
	];
    }
}
