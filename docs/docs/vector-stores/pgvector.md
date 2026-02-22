---
sidebar_position: 1
---

# PgVector Store

The `rag-php/pgvector` package stores embeddings in PostgreSQL using the [pgvector](https://github.com/pgvector/pgvector) extension.

## Installation

```bash
composer require rag-php/pgvector
```

## Requirements

- PostgreSQL 15+
- pgvector extension 0.5+

### Docker Setup

```yaml
# docker-compose.yml
services:
  pgvector:
    image: pgvector/pgvector:pg16
    environment:
      POSTGRES_DB: rag_db
      POSTGRES_USER: rag
      POSTGRES_PASSWORD: secret
    ports:
      - "5432:5432"
```

## Usage

```php
use Doctrine\DBAL\DriverManager;
use RagPhp\PgVector\PgVectorStore;

$connection = DriverManager::getConnection([
    'url' => 'postgresql://rag:secret@localhost:5432/rag_db',
]);

$store = new PgVectorStore(
    connection: $connection,
    tableName: 'rag_documents',  // default
    dimensions: 1536,             // match your embedder
);

// Create the table and pgvector extension
$store->createSchema();
```

## Schema

The `createSchema()` method creates:

1. The `vector` PostgreSQL extension
2. A table with columns: `id` (UUID), `content` (TEXT), `embedding` (vector), `metadata` (JSONB), `created_at`
3. An IVFFlat index for fast similarity search

## Metadata Filtering

```php
$results = $store->search(
    queryVector: $vector,
    topK: 10,
    filters: ['category' => 'support', 'language' => 'en'],
);
```

Filters match against the JSONB `metadata` column.
