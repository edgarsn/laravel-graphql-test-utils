# Laravel GraphQL testing utility

This package helps you to test your GraphQL queries & mutations in your TestCases.
It works with any laravel GraphQL server.

## Requirements
- Laravel 8.12+, 9.0+, 10.0+
- PHP 8.0+

## Installation
Require the package via Composer:

```bash
composer require newman/laravel-graphql-test-utils
```

# :book: Documentation & Usage

To start using it, import our trait in your TestCase

```php
<?php

namespace Tests;

use \Newman\LaravelGraphQLTestUtils\Traits\WithGraphQL;

class SomeTest extends TestCase {
    use WithGraphQL;

    public function test_it_returns_cars(): void
    {
        $response = $this
            ->graphql()
            ->setQuery('query ($search: String!) {
                cars(search: $search) {
                    brand
                }
            }')
            ->setVariables([
                'search' => 'BMW',
            ])
            ->call();
            
        $this->assertEquals([
            'cars' => [
                ['brand' => 'BMW'],
            ],
        ], $response->json('data'));
    }
}
```

Now you can explore our available builder methods.

### Default assertions

By default, **we don't assert anything for you** after response is retrieved, but we expose function to register your handler to customize this behaviour.

```php
<?php

namespace Tests;

use Newman\LaravelGraphQLTestUtils\GraphQLTesting;
use Newman\LaravelGraphQLTestUtils\TestResponse;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    
        GraphQlTesting::defaultAssertions(function (TestResponse $response): void {
            $response->assertOk()
                ->assertNoGraphQLErrors();
        });
    }
}
```

## Builder methods

### `setQuery`

Set your GraphQL query.

```php
$this
    ->graphql()
    ->setQuery('query {
        cars {
            brand
        }
    }')
    ->call();
```

### `setVariables`

Set multiple GraphQL variables as key-value pair.

```php
$this
    ->graphql()
    ->setVariables(['name' => 'John', 'email' => 'my@email.com'])
    ->call();

// Variables will result as: ['name' => 'John', 'email' => 'my@email.com']
```

**Note:** Calling `setVariables`, will replace all previously set variables. You may want to use `mergeVariables` in that case instead.

### `setVariable`

Set variables one-by-one.

```php
$this
    ->graphql()
    ->setVariable('name', 'Oliver')
    ->setVariable('surname', 'Smith')
    ->call();

// Variables will result as: ['name' => 'Oliver', 'surname' => 'Smith']
```

You can also mix it with `setVariables` and `mergeVariables`.

```php
$this
    ->graphql()
    ->setVariables(['name' => 'James', 'email' => 'my@email.com'])
    ->setVariable('name', 'Oliver')
    ->setVariable('surname', 'Smith')
    ->mergeVariables(['surname' => 'Williams', 'birthday' => '1990-01-01'])
    ->call();

// Variables will result as: ['name' => 'Oliver', 'email' => 'my@email.com', 'surname' => 'Williams', 'birthday' => '1990-01-01']
```

**Note:** Calling `setVariable` with the same key will override previous value.

### `mergeVariables`

Instead of resetting all variables like `setVariables` does, this method merges previously set variables with a pair of new variables.

```php
$this
    ->graphql()
    ->setVariables(['name' => 'John', 'email' => 'my@email.com'])
    ->mergeVariables(['name' => 'James'])
    ->call();

// Variables will result as: ['name' => 'James', 'email' => 'my@email.com']
```

### `schema`

Specifies which GraphQL schema (name) to use.

```php
$this
    ->graphql()
    ->schema('users')
    ->call();
```

**Note:** Depending on the driver used, it may automatically resolve HTTP method to use based on schema. [More](#drivers).

### `httpMethod`

Forces specific HTTP method to use. By default, it will resolve from schema (if driver provides that), otherwise `POST` will be used.

```php
$this
    ->graphql()
    ->httpMethod('GET')
    ->call();
```

### `driver`

Optionally you can switch this query to other driver than default. Probably you won't need this.

```php
$this
    ->graphql()
    ->driver('myCustom')
    ->call();
```

### `withToken`

Adds an Bearer Authorization token to request. 

```php
$this
    ->graphql()
    ->withToken('U88Itq0x3yHrhAgCa8mOWuUMKScGAX3zs0xHGnJnvHJoTOmpVTaDX2SVxwxQIsL8')
    ->call();
```

### `withoutToken`

Remove the authorization token from the request.

```php
$this
    ->graphql()
    ->withoutToken()
    ->call();
```

### `withHeader`

Add single header to request.

```php
$this
    ->graphql()
    ->withHeader('Authorization', 'Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==')
    ->call();
```

### `withHeader`

Add multiple headers to request.

```php
$this
    ->graphql()
    ->withHeaders([
        'X-User-Language' => 'en',
        'X-Client' => 'GraphQL-Test',
    ])
    ->call();
```

### `modifyRequest`

Since in base we use Laravel\`s `MakesHttpRequests` Trait, you can access those functions as well.

```php
use \Newman\LaravelGraphQLTestUtils\GraphQLRequest;

$this
    ->graphql()
    ->modifyRequest(function (GraphQLRequest $request) {
        $request->flushHeaders();
        
        // all MakesHttpRequest public methods can be called.
        
        // https://github.com/laravel/framework/blob/9.x/src/Illuminate/Foundation/Testing/Concerns/MakesHttpRequests.php
    })
    ->call();
```

### `withDefaultAssertions` and `withoutDefaultAssertions`

You may want to disable or re-enable default assertions individually.

Disable default assertions for this query:

```php
$this
    ->graphql()
    ->withoutDefaultAssertions()
    ->call();
```

```php
function getBaseBuilder() {
    return $this->graphql()->withoutDefaultAssertions();
}

// I want them to be enabled here.
getBaseBuilder()
    ->withDefaultAssertions()
    ->call();
```

### `call`

Makes the call to GraphQL and returns our `TestResponse` class. Function has 2 arguments

```php
$this
    ->graphql()
    ->call('query ($search: String!) { cars (search: $search) { brand } }', ['search' => 'BMW']);
```

**Note:** Variables passed to this function will merge with existing ones.

## Response

We extend default Laravel\`s `Illuminate\Testing\TestResponse` with a few GraphQL related functions/assertions.

### `assertNoGraphQLErrors`

Asserts no GraphQL errors were returned.

### `assertGraphQLUnauthorized`

Asserts there is an Unauthorized error in corresponding GraphQL error format.

### `getQuery`

Access used query in the request.

### `getVariables`

Access used variables in the request.

### `getGraphQLErrors`

Returns `array` of GraphQL errors or `null` when not present.

### `hasGraphQLErrors`

Determines if there are any GraphQL errors.

### `getGraphQLValidationMessages`

Get list of Laravel validation messages or empty array when none.

```php
[
    'name' => ['This field is required.', 'It must be a string'],
    // ...
]
```

### `getValidationFieldMessages`

Get Laravel validation messages on specific field or empty array when none or field is not present.

```php
$messages = $response->getValidationFieldMessages('name'); // ['This field is required.', 'It must be a string']
```

### `getValidationFieldFirstMessage`

Get first Laravel validation message on specific field or null when none or field is not present.

```php
$message = $response->getValidationFieldFirstMessage('name'); // 'This field is required.'
```

:information_source: Remember you can access all `Illuminate\Testing\TestResponse` functions as well.

:question: Missing frequent response helper? Open up a new issue.

## Custom Response class

This example shows you how to create your custom TestResponse class with your custom functions.

```php
<?php

declare(strict_types=1);

namespace App\Support;

use Newman\LaravelGraphQLTestUtils\TestResponse;

class CustomTestResponse extends TestResponse 
{

    public function assertDataAlwaysContainsAge(): static 
    {
        if (!$this->json('data.age')) {
            Assert::fail('Failed to assert that response contains age key.');
        }
    }
}
```

Then in your ServiceProvider (e.g. `AppServiceProvider`) or TestCase:

```php
<?php

namespace App\Providers;

use App\Support\CustomTestResponse;
use Newman\LaravelGraphQLTestUtils\GraphQLTesting;

class AppServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        GraphQLTesting::useCustomResponseHandler(CustomTestResponse::class);
    }
}
```

## Custom Builder class

This example shows you how to create your custom GraphQLBuilder class with your custom functions.

```php
<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Newman\LaravelGraphQLTestUtils\GraphQLBuilder;

class CustomGraphQLBuilder extends GraphQLBuilder 
{

    public function withUser(User $user): static 
    {
        $this->withToken($user->api_token)
    
        return $this;
    }
}
```

Then in your ServiceProvider (e.g. `AppServiceProvider`):

```php
<?php

namespace App\Providers;

use App\Support\CustomGraphQLBuilder;
use Newman\LaravelGraphQLTestUtils\Contracts\GraphQLBuilderContract;

class AppServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function register()
    {
        $this->app->bind(GraphQLBuilderContract::class, CustomGraphQLBuilder::class);
    }
}
```

## <a name="drivers"></a> Drivers

Drivers help you to construct request by pulling information from GraphQL server config, so you can discard some builder calls.
You can write your custom driver if none of ours fits your needs.

### RebingDriver (Default)

https://github.com/rebing/graphql-laravel

It reads URL prefix from config `graphql.route.prefix` and takes first HTTP method from config by schema name.

This is the default driver.

### NullDriver

It uses `/graphql` URL prefix and `POST` HTTP Method.

To use, call `GraphQLTesting::useNullDriver()` in your `AppServiceProvider.php` `boot()` method.

### Custom driver

Example on how to implement custom driver with name `myCustom`.

```php
<?php

declare(strict_types=1);

namespace App\Support\GraphQLTestingDrivers;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Container\Container;
use Newman\LaravelGraphQLTestUtils\Contracts\DriverContract;

class MyCustomDriver implements DriverContract
{
    /**
     * @var Container
     */
    protected $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function getUrlPrefix(): ?string
    {
        // ... return your url prefix to GraphQL endpoint. in this case it would be /my-graphql
        // returning null will fallback to default URL prefix.
        return 'my-graphql';
    }

    public function getHttpMethodForSchema(string $schemaName): ?string
    {
        // ... detect HTTP method from schema name and return it.
        // below is an example. You may read it from config or resolve any other way.
        // returning null will fallback to default HTTP method.
        return $schemaName == 'default' ? 'GET' : 'POST';
    }
}
```

Then in your ServiceProvider (e.g. `AppServiceProvider`):

```php
<?php

namespace App\Providers;

use App\Support\GraphQLTestingDrivers\MyCustomDriver;
use Newman\LaravelGraphQLTestUtils\GraphQLTesting;

class AppServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        // to activate this driver
        
        GraphQlTesting::useDriver('myCustom');
    }
    
    public function register()
    {
        // ... your other bindings
        $this->app->bind('laravel-graphql-utils-driver:myCustom', MyCustomDriver::class);
    }
}
```

## :handshake: Contributing

We'll appreciate your collaboration to this package. 

When making pull requests, make sure:
 * All tests are passing: `composer test`
 * Test coverage is not reduced: `composer test-coverage`
 * There are no PHPStan errors: `composer phpstan`
 * Coding standard is followed: `composer lint` or `composer fix-style` to automatically fix it. 
