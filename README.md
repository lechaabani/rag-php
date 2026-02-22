<p align="center">
  <img src="docs/static/img/logo.svg" width="120" alt="RAG-PHP Logo">
</p>

<h1 align="center">RAG-PHP</h1>

<p align="center">
  <strong>The reference Retrieval-Augmented Generation component for PHP</strong>
</p>

<p align="center">
  <a href="https://github.com/rag-php/rag-php/actions"><img src="https://github.com/rag-php/rag-php/workflows/CI/badge.svg" alt="CI Status"></a>
  <a href="https://packagist.org/packages/rag-php/core"><img src="https://img.shields.io/packagist/v/rag-php/core.svg" alt="Latest Version"></a>
  <a href="https://packagist.org/packages/rag-php/core"><img src="https://img.shields.io/packagist/php-v/rag-php/core.svg" alt="PHP Version"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License"></a>
</p>

---

RAG-PHP is a modular, framework-agnostic PHP component that brings Retrieval-Augmented Generation to the PHP ecosystem. It combines document retrieval with LLM generation to produce accurate, context-aware responses grounded in your own data.

## Features

- **Modular Architecture** — Clean interfaces, swap any component at runtime
- **Multiple LLM Providers** — OpenAI, Anthropic, Mistral, Ollama (local)
- **Vector Store Adapters** — PostgreSQL + pgvector, Qdrant, Pinecone, in-memory
- **Document Loaders** — PDF, HTML, Markdown, CSV, JSON
- **Smart Chunking** — Fixed-size, sentence-aware, recursive, semantic
- **Advanced Retrieval** — Similarity, MMR, HyDE, BM25, hybrid
- **Framework Bridges** — Symfony Bundle & Laravel ServiceProvider
- **Event System** — Hook into every pipeline stage
- **Streaming Support** — Server-Sent Events via PHP Generators

## Quick Start

```bash
composer require rag-php/core rag-php/openai rag-php/pgvector
```

```php
<?php

use RagPhp\Core\Pipeline\RagPipeline;
use RagPhp\Core\Retriever\SimilarityRetriever;
use RagPhp\OpenAI\OpenAIEmbedder;
use RagPhp\OpenAI\OpenAILlm;
use RagPhp\PgVector\PgVectorStore;

$rag = RagPipeline::create()
    ->withEmbedder(new OpenAIEmbedder($apiKey))
    ->withVectorStore(new PgVectorStore($connection))
    ->withRetriever(new SimilarityRetriever(topK: 5))
    ->withLlm(new OpenAILlm($apiKey, model: 'gpt-4o'))
    ->build();

// Index documents
$rag->index([
    new Document('Returns are accepted within 30 days...'),
    new Document('Shipping takes 3-5 business days...'),
]);

// Query
$response = $rag->query('How do I return a product?');

echo $response->getAnswer();      // Generated answer with context
echo $response->getSources();     // Source documents used
echo $response->getTokensUsed();  // Token usage metrics
```

## Packages

| Package | Description |
|---------|-------------|
| [`rag-php/core`](packages/core) | Interfaces, value objects, pipeline, events |
| [`rag-php/openai`](packages/openai) | OpenAI embedder & LLM adapter |
| [`rag-php/pgvector`](packages/pgvector) | PostgreSQL + pgvector store |
| [`rag-php/loaders`](packages/loaders) | Document loaders (PDF, HTML, CSV...) |

## Requirements

- PHP 8.2+
- Composer 2.0+
- PostgreSQL 15+ with pgvector (optional, for PgVectorStore)

## Documentation

Full documentation is available at [https://rag-php.github.io/rag-php](https://rag-php.github.io/rag-php).

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

RAG-PHP is open-sourced software licensed under the [MIT license](LICENSE).
