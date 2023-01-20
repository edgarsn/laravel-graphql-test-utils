<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Support\Collection;
use Illuminate\Testing\LoggedExceptionCollection;
use Illuminate\Testing\TestResponse as LaravelTestResponse;

class GraphQLRequest
{
    use MakesHttpRequests;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
	$this->app = $app;
    }

    /**
     * Create the test response instance from the given response.
     *
     * @param \Illuminate\Http\Response $response
     * @return LaravelTestResponse
     */
    protected function createTestResponse($response)
    {
	$testResponse = GraphQLTesting::getCustomResponseHandler();

	/** @var LaravelTestResponse $result */
	$result = tap(new $testResponse($response), function ($response) {
	    /** @var LaravelTestResponse $response */

	    // @codeCoverageIgnoreStart
	    /** @var Collection<int, mixed> $collection */
	    $collection = $this->app->bound(LoggedExceptionCollection::class)
		? $this->app->make(LoggedExceptionCollection::class)
		: new LoggedExceptionCollection;
	    // @codeCoverageIgnoreEnd

	    $response->withExceptions($collection);
	});

	return $result;
    }
}
