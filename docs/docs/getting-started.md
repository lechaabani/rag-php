---
sidebar_position: 2
---

# Getting Started

Get up and running with RAG-PHP in under 5 minutes.

## Installation

```bash
composer require rag-php/core rag-php/openai rag-php/pgvector
```

## Setup PostgreSQL + pgvector

If you're using Docker:

```bash
docker compose up -d
```

This starts a PostgreSQL 16 instance with the pgvector extension.

## Your First RAG Pipeline

```php
<?php

declare(strict_types=1);

use RagPhp\Core\Pipeline\RagPipeline;
use RagPhp\Core\Retriever\SimilarityRetriever;
use RagPhp\Core\ValueObject\Document;
use RagPhp\OpenAI\OpenAIEmbedder;
use RagPhp\OpenAI\OpenAILlm;
use RagPhp\PgVector\PgVectorStore;

// 1. Configure components
$apiKey = getenv('OPENAI_API_KEY');
$embedder = new OpenAIEmbedder($apiKey);
$vectorStore = new PgVectorStore($connection);
$retriever = new SimilarityRetriever($embedder, $vectorStore);
$llm = new OpenAILlm($apiKey, model: 'gpt-4o');

// 2. Build the pipeline
$rag = RagPipeline::create()
    ->withEmbedder($embedder)
    ->withVectorStore($vectorStore)
    ->withRetriever($retriever)
    ->withLlm($llm)
    ->build();

// 3. Index your documents
$rag->index([
    new Document('Returns are accepted within 30 days of purchase.'),
    new Document('Shipping takes 3-5 business days for standard delivery.'),
    new Document('Premium members get free expedited shipping.'),
]);

// 4. Query
$response = $rag->query('How long does shipping take?');

echo $response->getAnswer();
// "Standard shipping takes 3-5 business days. Premium members get free expedited shipping."

echo $response->getTokensUsed();
// 142
```

## Next Steps

- Learn about [RAG concepts](/docs/concepts/what-is-rag)
- Explore the [architecture](/docs/concepts/architecture)
- See the [API reference](/docs/api/embedder)
