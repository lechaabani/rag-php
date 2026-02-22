---
sidebar_position: 1
---

# EmbedderInterface

The `EmbedderInterface` converts text into vector embeddings — numerical representations that capture semantic meaning.

## Interface

```php
namespace RagPhp\Core\Contract;

interface EmbedderInterface
{
    /** @return list<float> */
    public function embed(string $text): array;

    /** @return list<list<float>> */
    public function embedBatch(array $texts): array;

    public function getDimensions(): int;
}
```

## Methods

### `embed(string $text): array`

Converts a single text string into a vector of floats.

```php
$vector = $embedder->embed('What is RAG?');
// [0.0123, -0.0456, 0.0789, ...] (1536 floats for OpenAI small)
```

### `embedBatch(array $texts): array`

Embeds multiple texts in a single API call for efficiency.

```php
$vectors = $embedder->embedBatch([
    'First document',
    'Second document',
    'Third document',
]);
// Returns 3 vectors
```

### `getDimensions(): int`

Returns the dimensionality of the embedding model. Used to configure vector stores.

```php
$dims = $embedder->getDimensions(); // 1536
```

## Available Implementations

| Implementation | Package | Models |
|---------------|---------|--------|
| `OpenAIEmbedder` | `rag-php/openai` | text-embedding-3-small, text-embedding-3-large, ada-002 |
| `OllamaEmbedder` | `rag-php/ollama` | nomic-embed-text, mxbai-embed-large (coming soon) |
