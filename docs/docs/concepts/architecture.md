---
sidebar_position: 2
---

# Architecture

RAG-PHP follows a layered architecture with clean separation of concerns.

## Layers

```
┌─────────────────────────────────────────────────┐
│              APPLICATION LAYER                  │
│     (Symfony Bundle / Laravel ServiceProvider)  │
├─────────────────────────────────────────────────┤
│                 CORE LAYER                      │
│    RagPipeline  |  EventDispatcher  |  Builder  │
├──────────┬──────────┬──────────┬───────────────┤
│ Embedder │VectorStore│ Retriever│  LLM Client   │
├──────────┼──────────┼──────────┼───────────────┤
│ OpenAI   │ PgVector │Similarity│  OpenAI GPT   │
│ Ollama   │ Qdrant   │  MMR     │  Mistral      │
│ Mistral  │ InMemory │  HyDE    │  Anthropic    │
└──────────┴──────────┴──────────┴───────────────┘
```

## Core Interfaces

The entire system is built around four key interfaces:

| Interface | Responsibility |
|-----------|---------------|
| `EmbedderInterface` | Convert text to vector embeddings |
| `VectorStoreInterface` | Store and search document vectors |
| `RetrieverInterface` | Find relevant documents for a query |
| `LlmInterface` | Generate text completions |

## Design Patterns

| Pattern | Applied To | Benefit |
|---------|-----------|---------|
| **Builder** | `RagPipeline::create()` | Fluent API, progressive construction |
| **Strategy** | Retrievers, Chunkers | Interchangeable algorithms at runtime |
| **Adapter** | OpenAI, Ollama providers | Isolation from external APIs |
| **Observer** | Pipeline events | Extensibility without core modification |
| **Value Object** | Document, Vector, Response | Immutability, data safety |

## Package Structure

```
rag-php/
├── packages/
│   ├── core/       → Interfaces + Value Objects + Pipeline
│   ├── openai/     → OpenAI Embedder + LLM
│   ├── pgvector/   → PostgreSQL + pgvector Store
│   ├── loaders/    → Document loaders
│   ├── symfony/    → Symfony Bundle
│   └── laravel/    → Laravel ServiceProvider
└── docs/           → This documentation
```

Each package is independently installable via Composer and follows [SemVer 2.0](https://semver.org/).
