---
sidebar_position: 2
---

# InMemory Store

The `InMemoryVectorStore` is included in `rag-php/core` and stores everything in PHP memory. It's perfect for testing and development.

## Usage

```php
use RagPhp\Core\Store\InMemoryVectorStore;

$store = new InMemoryVectorStore();

// Store documents
$store->store($document, $vector, ['source' => 'test']);

// Search
$results = $store->search($queryVector, topK: 5);

// Utilities
$store->count();  // Number of stored documents
$store->clear();  // Remove all documents
```

## When to Use

- **Unit tests** — No external dependencies needed
- **Development** — Quick iteration without database setup
- **Prototyping** — Test your pipeline logic before choosing a production store

## Limitations

- Data is lost when the process ends
- Not suitable for large datasets (everything lives in RAM)
- No persistence or replication
