---
sidebar_position: 2
---

# VectorStoreInterface

The `VectorStoreInterface` handles persisting document embeddings and performing similarity searches.

## Interface

```php
namespace RagPhp\Core\Contract;

interface VectorStoreInterface
{
    public function store(Document $document, array $vector, array $metadata = []): string;
    public function search(array $queryVector, int $topK = 5, array $filters = []): array;
    public function delete(string $id): void;
    public function clear(): void;
}
```

## Methods

### `store()`

Persists a document with its embedding vector and optional metadata. Returns the document ID.

```php
$id = $store->store(
    new Document('Product returns policy...'),
    $embedder->embed('Product returns policy...'),
    ['category' => 'support', 'source' => 'faq.md'],
);
```

### `search()`

Finds the most similar documents to a query vector. Supports metadata filtering.

```php
$results = $store->search(
    queryVector: $embedder->embed('How to return?'),
    topK: 5,
    filters: ['category' => 'support'],
);

foreach ($results as $doc) {
    echo $doc->content;   // Document text
    echo $doc->score;     // Similarity score (0-1)
}
```

### `delete()` / `clear()`

Remove individual documents or clear the entire store.

## Available Implementations

| Implementation | Package | Best For |
|---------------|---------|----------|
| `PgVectorStore` | `rag-php/pgvector` | Production with PostgreSQL |
| `QdrantStore` | `rag-php/qdrant` | High-volume, advanced filtering (coming soon) |
| `InMemoryVectorStore` | `rag-php/core` | Testing and development |
