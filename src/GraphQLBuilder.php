<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils;

use Illuminate\Contracts\Foundation\Application;
use Newman\LaravelGraphQLTestUtils\Contracts\GraphQLBuilderContract;
use Newman\LaravelGraphQLTestUtils\Contracts\GraphQLTestingContract;
use Newman\LaravelGraphQLTestUtils\Exceptions\GraphQLQueryNotProvidedException;

class GraphQLBuilder implements GraphQLBuilderContract
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var GraphQLRequest
     */
    protected $request;

    /**
     * @var string|null
     */
    protected $driverName = null;

    /**
     * @var string
     */
    protected $schemaName = 'default';

    /**
     * @var string|null
     */
    protected $httpMethod = null;

    /**
     * @var string|null
     */
    protected $query = null;

    /**
     * @var array<string, mixed>
     */
    protected $variables = [];

    /**
     * @var bool
     */
    protected $withoutDefaultAssertions = false;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->request = new GraphQLRequest($app);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function withHeader(string $name, string $value): static
    {
        $this->request->withHeader($name, $value);

        return $this;
    }

    /**
     * @param  array<string, string>  $headers
     *
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function withHeaders(array $headers): static
    {
        $this->request->withHeaders($headers);

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function withToken(string $apiToken): static
    {
        $this->request->withToken($apiToken);

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function withoutToken(): static
    {
        $this->request->withoutToken();

        return $this;
    }

    /**
     * @param  callable(GraphQLRequest $request): void  $callback
     *
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function modifyRequest(callable $callback): static
    {
        $callback($this->request);

        return $this;
    }

    public function driver(string $name): static
    {
        $this->driverName = $name;

        return $this;
    }

    public function httpMethod(string $method): static
    {
        $this->httpMethod = $method;

        return $this;
    }

    public function schema(string $schema): static
    {
        $this->schemaName = $schema;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function setQuery(string $query): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $variables
     *
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function setVariables(array $variables): static
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $variables
     * @return $this
     */
    public function mergeVariables(array $variables): static
    {
        $this->variables = array_merge($this->variables, $variables);

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function setVariable(string $key, mixed $value): static
    {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function withDefaultAssertions(bool $with = true): static
    {
        $this->withoutDefaultAssertions = ! $with;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function withoutDefaultAssertions(): static
    {
        return $this->withDefaultAssertions(false);
    }

    /**
     * @param  array<string, mixed>  $variables
     */
    public function call(?string $query = null, array $variables = []): TestResponse
    {
        $query = $query ?? $this->query;
        $variables = array_merge($this->variables, $variables);

        if (empty($query)) {
            throw new GraphQLQueryNotProvidedException('GraphQL query is not provided.');
        }

        $uri = $this->resolveBaseUrl();
        $httpMethod = $this->resolveHttpMethod();

        $data = [
            'query' => $query,
            'variables' => $variables,
        ];

        if ($httpMethod == 'POST') {
            /** @var TestResponse $response */
            $response = $this->request->post($uri, $data);
        } else {
            $data['variables'] = json_encode($data['variables']);

            /** @var TestResponse $response */
            $response = $this->request->get($uri.'?'.http_build_query($data));
        }

        $response->setQuery($query);
        $response->setVariables($variables);

        $this->onAfterRequest($query, $variables, $response);

        return $response;
    }

    protected function resolveDriverName(): string
    {
        return $this->driverName ?? GraphQLTesting::getDriverName();
    }

    protected function resolveUrlPrefix(): string
    {
        /** @var GraphQLTestingContract $graphqlTesting */
        $graphqlTesting = $this->app->make(GraphQLTestingContract::class);

        return $graphqlTesting->driver($this->resolveDriverName())->getUrlPrefix() ?? 'graphql';
    }

    protected function resolveBaseUrl(): string
    {
        $uri = '/'.$this->resolveUrlPrefix();

        if ($this->schemaName != 'default') {
            $uri .= '/'.$this->schemaName;
        }

        return $uri;
    }

    protected function resolveHttpMethod(): string
    {
        /** @var GraphQLTestingContract $graphqlTesting */
        $graphqlTesting = $this->app->make(GraphQLTestingContract::class);

        return strtoupper($this->httpMethod ?? $graphqlTesting->driver($this->resolveDriverName())->getHttpMethodForSchema($this->schemaName) ?? 'POST');
    }

    protected function callDefaultAssertions(TestResponse $response): void
    {
        $defaultAssertions = GraphQLTesting::getDefaultAssertions();

        if (is_callable($defaultAssertions)) {
            $defaultAssertions($response);
        }
    }

    /**
     * @param  array<string, mixed>  $variables
     */
    protected function onAfterRequest(string $query, array $variables, TestResponse $response): void
    {
        if (! $this->withoutDefaultAssertions) {
            $this->callDefaultAssertions($response);
        }
    }
}
