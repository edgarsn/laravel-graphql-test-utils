<?php

declare(strict_types=1);

namespace Newman\LaravelGraphQLTestUtils;

use Illuminate\Support\Arr;
use Illuminate\Testing\Assert;
use Illuminate\Testing\TestResponse as LaravelTestResponse;

/**
 * @extends LaravelTestResponse<\Symfony\Component\HttpFoundation\Response>
 */
class TestResponse extends LaravelTestResponse
{
    /**
     * @var string
     */
    private $query;

    /**
     * @var array<string, mixed>
     */
    private $variables = [];

    public function setQuery(string $query): void
    {
	$this->query = $query;
    }

    public function getQuery(): string
    {
	return $this->query;
    }

    /**
     * @param array<string, mixed> $variables
     * @return void
     */
    public function setVariables(array $variables): void
    {
	$this->variables = $variables;
    }

    /**
     * @return array<string, mixed>
     */
    public function getVariables(): array
    {
	return $this->variables;
    }

    public function getVariable(string $key): mixed
    {
	return array_key_exists($key, $this->variables) ? $this->variables[$key] : null;
    }

    /**
     * @return array<array<string, mixed>>|null
     */
    public function getGraphQLErrors(): ?array
    {
	/** @var array<array<string, mixed>>|null $errors */
	$errors = $this->json('errors');

	return $errors;
    }

    public function hasGraphQLErrors(): bool
    {
	return !empty($this->json('errors'));
    }

    /**
     * @return array<string, array<string>>
     */
    public function getGraphQLValidationMessages(): array
    {
	$messages = $this->json('errors.0.extensions.validation') ?? [];

	return is_array($messages) ? $messages : [];
    }

    /**
     * @param string $field
     * @return array<string>
     */
    public function getValidationFieldMessages(string $field): array
    {
	$messages = $this->getGraphQLValidationMessages();

	if (empty($messages)) {
	    return [];
	}

	/** @var array<string> $fieldMessages */
	$fieldMessages = Arr::get($messages, $field, []);

	return $fieldMessages;
    }

    public function getValidationFieldFirstMessage(string $field): ?string
    {
	/** @var string|null $firstMessage */
	$firstMessage = Arr::first($this->getValidationFieldMessages($field));

	return $firstMessage;
    }

    /**
     * @codeCoverageIgnore
     * @return $this
     */
    public function assertNoGraphQLErrors(): static
    {
	if ($this->hasGraphQLErrors()) {
	    Assert::fail('Failed to assert that response has no errors.');
	}

	return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return $this
     */
    public function assertGraphQLUnauthorized(): static
    {
	$firstErrorMessage = $this->json('errors.0.message') ?? null;

	Assert::assertEquals('Unauthorized', $firstErrorMessage);

	return $this;
    }
}
