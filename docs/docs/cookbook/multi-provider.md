---
sidebar_position: 4
---

# Recipe: Mix and Match Providers

Use different providers for embeddings and generation to optimize cost, speed, or quality.

## Cheap Embeddings + Premium Generation

```php
use RagPhp\Mistral\MistralEmbedder;
use RagPhp\Anthropic\AnthropicLlm;

// Mistral embeddings are cheaper
$embedder = new MistralEmbedder($mistralKey, model: 'mistral-embed');

// Claude for high-quality generation
$llm = new AnthropicLlm($anthropicKey, model: 'claude-sonnet-4-5-20250929');
```

## Google Embeddings + OpenAI Generation

```php
use RagPhp\Gemini\GeminiEmbedder;
use RagPhp\OpenAI\OpenAILlm;

$embedder = new GeminiEmbedder($geminiKey, model: 'text-embedding-004');
$llm = new OpenAILlm($openaiKey, model: 'gpt-4o');
```

## Local Embeddings + Cloud Generation

```php
use RagPhp\Ollama\OllamaEmbedder;
use RagPhp\OpenAI\OpenAILlm;

// Free local embeddings
$embedder = new OllamaEmbedder(model: 'nomic-embed-text');

// Cloud generation for quality
$llm = new OpenAILlm($openaiKey, model: 'gpt-4o-mini');
```

## Provider Comparison

| Provider | Embedder | LLM | Cost | Local |
|----------|----------|-----|------|-------|
| OpenAI | text-embedding-3-small | GPT-4o | $$ | No |
| Gemini | text-embedding-004 | Gemini 2.0 Flash | $ | No |
| Ollama | nomic-embed-text | Llama 3 | Free | Yes |
| Anthropic | — | Claude Sonnet | $$$ | No |
| Mistral | mistral-embed | Mistral Large | $$ | No |
