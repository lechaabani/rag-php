# RAG-PHP Skeleton

A starter project for building RAG applications with PHP.

## Quick Start

```bash
# Create a new project
composer create-project rag-php/skeleton my-rag-app
cd my-rag-app

# Configure
cp .env.example .env
# Edit .env and set your API key

# Start PostgreSQL + pgvector
docker compose -f docker/docker-compose.yml up -d

# Run the example
php src/example.php
```

## Using Different Providers

Edit `.env` to switch providers:

```env
# OpenAI (default)
RAG_LLM_PROVIDER=openai
OPENAI_API_KEY=sk-...

# Google Gemini
RAG_LLM_PROVIDER=gemini
GEMINI_API_KEY=...

# Local with Ollama (free!)
RAG_LLM_PROVIDER=ollama
RAG_LLM_MODEL=llama3

# Anthropic Claude
RAG_LLM_PROVIDER=anthropic
ANTHROPIC_API_KEY=sk-ant-...

# Mistral
RAG_LLM_PROVIDER=mistral
MISTRAL_API_KEY=...
```

## CLI Commands

```bash
# Index documents
php bin/rag rag:index ./docs --format=md

# Query
php bin/rag rag:query "How do I return a product?" --show-sources

# Stats
php bin/rag rag:stats

# Test all connections
php bin/rag rag:test-connection
```

## Next Steps

- Read the [full documentation](https://rag-php.github.io/rag-php)
- Add your own documents to `./docs/`
- Integrate with [Symfony](https://rag-php.github.io/rag-php/docs/frameworks/symfony) or [Laravel](https://rag-php.github.io/rag-php/docs/frameworks/laravel)
