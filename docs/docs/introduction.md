---
sidebar_position: 1
slug: /
---

# Introduction

**RAG-PHP** is the reference Retrieval-Augmented Generation component for the PHP ecosystem.

It provides a modular, framework-agnostic architecture for building RAG pipelines — combining document retrieval with LLM generation to produce accurate, context-aware responses grounded in your own data.

## Why RAG-PHP?

- **Clean Interfaces** — Every component is behind a stable interface. Swap embedders, vector stores, or LLMs without touching your pipeline code.
- **Multiple Providers** — OpenAI, Anthropic, Mistral, and Ollama (local) out of the box.
- **PHP Native** — Built with PHP 8.2+ features: readonly classes, enums, strict types, generators for streaming.
- **Framework Bridges** — Official Symfony Bundle and Laravel ServiceProvider.
- **Tested & Documented** — PHPStan level 9, 95%+ test coverage, comprehensive documentation.

## Packages

| Package | Description |
|---------|-------------|
| `rag-php/core` | Interfaces, value objects, pipeline, events |
| `rag-php/openai` | OpenAI embedder & LLM adapter |
| `rag-php/pgvector` | PostgreSQL + pgvector store |
| `rag-php/loaders` | Document loaders (PDF, HTML, CSV) |
| `rag-php/symfony` | Symfony Bundle (coming soon) |
| `rag-php/laravel` | Laravel ServiceProvider (coming soon) |

## Requirements

- PHP 8.2+
- Composer 2.0+
