<?php

declare(strict_types=1);

namespace RagPhp\Core\Store;

use RagPhp\Core\Contract\VectorStoreInterface;
use RagPhp\Core\ValueObject\Document;
use RagPhp\Core\ValueObject\Vector;

final class InMemoryVectorStore implements VectorStoreInterface
{
    /** @var array<string, array{document: Document, vector: Vector, metadata: array<string, mixed>}> */
    private array $documents = [];

    private int $autoIncrement = 0;

    /**
     * @param list<float> $vector
     * @param array<string, mixed> $metadata
     */
    public function store(Document $document, array $vector, array $metadata = []): string
    {
        $id = $document->id !== '' ? $document->id : (string) ++$this->autoIncrement;
        $document = $document->withId($id);

        $this->documents[$id] = [
            'document' => $document,
            'vector' => new Vector($vector),
            'metadata' => $metadata,
        ];

        return $id;
    }

    /**
     * @param list<float> $queryVector
     * @param array<string, mixed> $filters
     * @return list<Document>
     */
    public function search(array $queryVector, int $topK = 5, array $filters = []): array
    {
        $query = new Vector($queryVector);
        $scored = [];

        foreach ($this->documents as $entry) {
            if (!$this->matchesFilters($entry['metadata'], $filters)) {
                continue;
            }

            $similarity = $query->cosineSimilarity($entry['vector']);
            $scored[] = [
                'document' => $entry['document']->withScore($similarity),
                'score' => $similarity,
            ];
        }

        usort($scored, static fn (array $a, array $b): int => $b['score'] <=> $a['score']);

        return array_map(
            static fn (array $entry): Document => $entry['document'],
            array_slice($scored, 0, $topK),
        );
    }

    public function delete(string $id): void
    {
        unset($this->documents[$id]);
    }

    public function clear(): void
    {
        $this->documents = [];
        $this->autoIncrement = 0;
    }

    public function count(): int
    {
        return count($this->documents);
    }

    /**
     * @param array<string, mixed> $metadata
     * @param array<string, mixed> $filters
     */
    private function matchesFilters(array $metadata, array $filters): bool
    {
        foreach ($filters as $key => $value) {
            if (!isset($metadata[$key]) || $metadata[$key] !== $value) {
                return false;
            }
        }

        return true;
    }
}
