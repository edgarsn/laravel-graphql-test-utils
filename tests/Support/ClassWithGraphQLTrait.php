<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils\Tests\Support;

use Illuminate\Contracts\Foundation\Application;
use Newman\LaravelGraphQLTestUtils\Traits\WithGraphQL;

class ClassWithGraphQLTrait
{
    use WithGraphQL;

    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
