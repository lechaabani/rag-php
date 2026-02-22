---
sidebar_position: 3
---

# Recipe: Free Local RAG with Ollama

Run RAG entirely on your machine with no API costs using Ollama.

## Prerequisites

Install Ollama: https://ollama.com

```bash
# Pull the models
ollama pull nomic-embed-text   # embeddings
ollama pull llama3             # generation
```

## Implementation

```php
use RagPhp\Core\Pipeline\RagPipeline;
use RagPhp\Core\Retriever\SimilarityRetriever;
use RagPhp\Core\Store\InMemoryVectorStore;
use RagPhp\Core\ValueObject\Document;
use RagPhp\Ollama\OllamaEmbedder;
use RagPhp\Ollama\OllamaLlm;

// No API key needed!
$embedder = new OllamaEmbedder(model: 'nomic-embed-text');
$store = new InMemoryVectorStore();
$retriever = new SimilarityRetriever($embedder, $store);
$llm = new OllamaLlm(model: 'llama3');

$rag = RagPipeline::create()
    ->withEmbedder($embedder)
    ->withVectorStore($store)
    ->withRetriever($retriever)
    ->withLlm($llm)
    ->build();

// Index and query — same API as cloud providers
$rag->index([new Document('Your private data stays local.')]);
$response = $rag->query('Where is my data stored?');
echo $response->getAnswer();
```

## Remote Ollama Server

```php
// Point to a remote GPU server
$embedder = new OllamaEmbedder('http://gpu-server:11434', model: 'nomic-embed-text');
$llm = new OllamaLlm('http://gpu-server:11434', model: 'llama3');
```

## When to Use

- **Privacy-sensitive data** — nothing leaves your network
- **Development/testing** — no API costs
- **Air-gapped environments** — no internet required
- **GPU servers** — run larger models on dedicated hardware
