---
sidebar_position: 1
---

# OpenAI Provider

The `rag-php/openai` package provides OpenAI-based embeddings and LLM completions.

## Installation

```bash
composer require rag-php/openai
```

## Embedder

```php
use RagPhp\OpenAI\OpenAIEmbedder;

$embedder = new OpenAIEmbedder(
    config: $apiKey,                   // or OpenAIConfiguration object
    model: 'text-embedding-3-small',   // default
);
```

### Supported Models

| Model | Dimensions | Cost | Best For |
|-------|-----------|------|----------|
| `text-embedding-3-small` | 1536 | Lowest | Most use cases |
| `text-embedding-3-large` | 3072 | Medium | Higher accuracy |
| `text-embedding-ada-002` | 1536 | Medium | Legacy compatibility |

## LLM

```php
use RagPhp\OpenAI\OpenAILlm;

$llm = new OpenAILlm(
    config: $apiKey,
    model: 'gpt-4o',          // default
    temperature: 0.2,          // default
    maxTokens: 2048,           // default
);
```

### Supported Models

| Model | Speed | Quality | Cost |
|-------|-------|---------|------|
| `gpt-4o` | Fast | Highest | Higher |
| `gpt-4o-mini` | Fastest | High | Lowest |
| `gpt-3.5-turbo` | Fast | Good | Low |

## Configuration Object

For advanced configuration:

```php
use RagPhp\OpenAI\OpenAIConfiguration;

$config = new OpenAIConfiguration(
    apiKey: 'sk-...',
    organization: 'org-...',   // optional
);

$embedder = new OpenAIEmbedder($config);
$llm = new OpenAILlm($config);
```

## Streaming

```php
foreach ($llm->stream($prompt, $context) as $chunk) {
    echo $chunk;
    flush();
}
```
