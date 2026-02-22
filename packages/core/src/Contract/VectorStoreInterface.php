<?php

declare(strict_types=1);

namespace RagPhp\Core\Contract;

use RagPhp\Core\ValueObject\Document;

interface VectorStoreInterface
{
    /**
     * Store a document with its embedding vector and optional metadata.
     *
     * @param list<float> $vector
     * @param array<string, mixed> $metadata
     * @return string The stored document ID
     */
    public function store(Document $document, array $vector, array $metadata = []): string;

    /**
     * Search for similar documents by query vector.
     *
     * @param list<float> $queryVector
     * @param array<string, mixed> $filters
     * @return list<Document>
     */
    public function search(array $queryVector, int $topK = 5, array $filters = []): array;

    /**
     * Delete a document by its ID.
     */
    public function delete(string $id): void;

    /**
     * Remove all documents from the store.
     */
    public function clear(): void;
}
