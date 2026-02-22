<?php

declare(strict_types=1);

namespace RagPhp\PgVector\Tests\Unit;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Core\Exception\VectorStoreException;
use RagPhp\Core\ValueObject\Document;
use RagPhp\PgVector\PgVectorStore;

#[CoversClass(PgVectorStore::class)]
final class PgVectorStoreTest extends TestCase
{
    #[Test]
    public function it_stores_a_document(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('executeStatement')
            ->with(self::stringContains('INSERT INTO rag_documents'));

        $store = new PgVectorStore($connection);
        $id = $store->store(new Document('test content', 'doc-1'), [1.0, 0.0]);

        self::assertSame('doc-1', $id);
    }

    #[Test]
    public function it_generates_id_when_not_provided(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('executeStatement');

        $store = new PgVectorStore($connection);
        $id = $store->store(new Document('test'), [1.0]);

        self::assertNotEmpty($id);
        self::assertMatchesRegularExpression('/^[0-9a-f-]{36}$/', $id);
    }

    #[Test]
    public function it_deletes_a_document(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('executeStatement')
            ->with(
                self::stringContains('DELETE FROM rag_documents'),
                ['id' => 'doc-1'],
            );

        $store = new PgVectorStore($connection);
        $store->delete('doc-1');
    }

    #[Test]
    public function it_clears_the_store(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('executeStatement')
            ->with('TRUNCATE TABLE rag_documents');

        $store = new PgVectorStore($connection);
        $store->clear();
    }

    #[Test]
    public function it_wraps_exceptions_in_vector_store_exception(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('executeStatement')
            ->willThrowException(new \RuntimeException('DB error'));

        $store = new PgVectorStore($connection);

        $this->expectException(VectorStoreException::class);
        $this->expectExceptionMessageMatches('/Failed to store document/');

        $store->store(new Document('test'), [1.0]);
    }

    #[Test]
    public function it_searches_with_filters(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::stringContains("metadata->>'category'"),
                self::callback(fn (array $params) => $params['filter_category'] === 'tech'),
            )
            ->willReturn([]);

        $store = new PgVectorStore($connection);
        $results = $store->search([1.0], 5, ['category' => 'tech']);

        self::assertSame([], $results);
    }

    #[Test]
    public function it_returns_documents_from_search(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('fetchAllAssociative')
            ->willReturn([
                [
                    'id' => 'doc-1',
                    'content' => 'test content',
                    'metadata' => '{"source":"file.pdf"}',
                    'score' => 0.95,
                ],
            ]);

        $store = new PgVectorStore($connection);
        $results = $store->search([1.0, 0.5]);

        self::assertCount(1, $results);
        self::assertSame('test content', $results[0]->content);
        self::assertSame('doc-1', $results[0]->id);
        self::assertSame(0.95, $results[0]->score);
        self::assertSame('file.pdf', $results[0]->metadata['source']);
    }
}
