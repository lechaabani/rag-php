<?php

declare(strict_types=1);

namespace RagPhp\PgVector;

use Doctrine\DBAL\Connection;
use RagPhp\Core\Contract\VectorStoreInterface;
use RagPhp\Core\Exception\VectorStoreException;
use RagPhp\Core\ValueObject\Document;

final class PgVectorStore implements VectorStoreInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $tableName = 'rag_documents',
        private readonly int $dimensions = 1536,
    ) {
    }

    /**
     * Create the required table and pgvector extension if they don't exist.
     */
    public function createSchema(): void
    {
        try {
            $this->connection->executeStatement('CREATE EXTENSION IF NOT EXISTS vector');

            $this->connection->executeStatement(sprintf(
                'CREATE TABLE IF NOT EXISTS %s (
                    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                    content TEXT NOT NULL,
                    embedding vector(%d) NOT NULL,
                    metadata JSONB DEFAULT \'{}\'::jsonb,
                    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
                )',
                $this->tableName,
                $this->dimensions,
            ));

            $this->connection->executeStatement(sprintf(
                'CREATE INDEX IF NOT EXISTS idx_%s_embedding ON %s USING ivfflat (embedding vector_cosine_ops) WITH (lists = 100)',
                $this->tableName,
                $this->tableName,
            ));
        } catch (\Throwable $e) {
            throw new VectorStoreException(
                sprintf('Failed to create schema: %s', $e->getMessage()),
                previous: $e,
            );
        }
    }

    /**
     * @param list<float> $vector
     * @param array<string, mixed> $metadata
     */
    public function store(Document $document, array $vector, array $metadata = []): string
    {
        try {
            $vectorString = '[' . implode(',', $vector) . ']';
            $mergedMetadata = array_merge($document->metadata, $metadata);

            $id = $document->id !== '' ? $document->id : $this->generateUuid();

            $this->connection->executeStatement(
                sprintf(
                    'INSERT INTO %s (id, content, embedding, metadata) VALUES (:id, :content, :embedding, :metadata)
                     ON CONFLICT (id) DO UPDATE SET content = :content, embedding = :embedding, metadata = :metadata',
                    $this->tableName,
                ),
                [
                    'id' => $id,
                    'content' => $document->content,
                    'embedding' => $vectorString,
                    'metadata' => json_encode($mergedMetadata, JSON_THROW_ON_ERROR),
                ],
            );

            return $id;
        } catch (\Throwable $e) {
            throw new VectorStoreException(
                sprintf('Failed to store document: %s', $e->getMessage()),
                previous: $e,
            );
        }
    }

    /**
     * @param list<float> $queryVector
     * @param array<string, mixed> $filters
     * @return list<Document>
     */
    public function search(array $queryVector, int $topK = 5, array $filters = []): array
    {
        try {
            $vectorString = '[' . implode(',', $queryVector) . ']';

            $whereClauses = [];
            $params = ['embedding' => $vectorString, 'limit' => $topK];

            foreach ($filters as $key => $value) {
                $paramName = 'filter_' . $key;
                $whereClauses[] = sprintf("metadata->>'%s' = :%s", $key, $paramName);
                $params[$paramName] = (string) $value;
            }

            $whereSQL = $whereClauses !== [] ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

            $sql = sprintf(
                'SELECT id, content, metadata, 1 - (embedding <=> :embedding) AS score
                 FROM %s
                 %s
                 ORDER BY embedding <=> :embedding
                 LIMIT :limit',
                $this->tableName,
                $whereSQL,
            );

            $rows = $this->connection->fetchAllAssociative($sql, $params);

            return array_map(static function (array $row): Document {
                /** @var array<string, mixed> $metadata */
                $metadata = json_decode((string) $row['metadata'], true, 512, JSON_THROW_ON_ERROR);

                return new Document(
                    content: (string) $row['content'],
                    id: (string) $row['id'],
                    metadata: $metadata,
                    score: (float) $row['score'],
                );
            }, $rows);
        } catch (\Throwable $e) {
            throw new VectorStoreException(
                sprintf('Search failed: %s', $e->getMessage()),
                previous: $e,
            );
        }
    }

    public function delete(string $id): void
    {
        try {
            $this->connection->executeStatement(
                sprintf('DELETE FROM %s WHERE id = :id', $this->tableName),
                ['id' => $id],
            );
        } catch (\Throwable $e) {
            throw new VectorStoreException(
                sprintf('Failed to delete document: %s', $e->getMessage()),
                previous: $e,
            );
        }
    }

    public function clear(): void
    {
        try {
            $this->connection->executeStatement(sprintf('TRUNCATE TABLE %s', $this->tableName));
        } catch (\Throwable $e) {
            throw new VectorStoreException(
                sprintf('Failed to clear store: %s', $e->getMessage()),
                previous: $e,
            );
        }
    }

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff), random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff),
        );
    }
}
