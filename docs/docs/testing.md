---
sidebar_position: 9
---

# Testing

RAG-PHP is designed to be easy to test. The `InMemoryVectorStore` and mockable interfaces make unit testing straightforward.

## Unit Testing with InMemoryVectorStore

```php
use PHPUnit\Framework\TestCase;
use RagPhp\Core\Store\InMemoryVectorStore;
use RagPhp\Core\ValueObject\Document;

class MyRagServiceTest extends TestCase
{
    public function test_it_stores_and_retrieves_documents(): void
    {
        $store = new InMemoryVectorStore();

        $store->store(
            new Document('Returns are accepted within 30 days'),
            [1.0, 0.5, 0.0],
        );

        $results = $store->search([1.0, 0.4, 0.1], topK: 1);

        self::assertCount(1, $results);
        self::assertStringContainsString('Returns', $results[0]->content);
    }
}
```

## Mocking Interfaces

All core components are behind interfaces, making them easy to mock:

```php
$embedder = $this->createMock(EmbedderInterface::class);
$embedder->method('embed')->willReturn([1.0, 0.0, 0.0]);
$embedder->method('embedBatch')->willReturn([[1.0, 0.0], [0.0, 1.0]]);
$embedder->method('getDimensions')->willReturn(3);

$llm = $this->createMock(LlmInterface::class);
$llm->method('complete')->willReturn(
    new Response('Mocked answer', [], 50)
);
```

## Integration Testing

For integration tests with a real PostgreSQL + pgvector instance, use Docker:

```bash
docker compose up -d
vendor/bin/phpunit --testsuite=Integration
```

## Running the Full Suite

```bash
# All tests
composer test

# Specific package
vendor/bin/phpunit --testsuite=Core
vendor/bin/phpunit --testsuite=OpenAI
vendor/bin/phpunit --testsuite=PgVector

# With coverage
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html=coverage
```
