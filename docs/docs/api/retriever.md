---
sidebar_position: 4
---

# RetrieverInterface

The `RetrieverInterface` selects the most relevant documents for a given query before injecting them into the LLM prompt.

## Interface

```php
namespace RagPhp\Core\Contract;

interface RetrieverInterface
{
    /** @return list<Document> */
    public function retrieve(string $query, int $topK = 5): array;
}
```

## Usage

```php
$retriever = new SimilarityRetriever($embedder, $vectorStore);
$documents = $retriever->retrieve('How to return a product?', topK: 5);
```

## Available Strategies

| Strategy | Description | Best For |
|----------|-------------|----------|
| `SimilarityRetriever` | Cosine similarity search | General use, fastest |
| `MMRRetriever` | Maximal Marginal Relevance | Diverse results, avoid redundancy |
| `HyDERetriever` | Hypothetical Document Embeddings | Better recall for complex queries |
| `BM25Retriever` | Lexical keyword search | Exact term matching |
| `HybridRetriever` | Combines vector + BM25 | Best of both worlds |

### SimilarityRetriever (Phase 1)

The simplest and fastest retriever. Embeds the query and searches the vector store by cosine similarity.

```php
use RagPhp\Core\Retriever\SimilarityRetriever;

$retriever = new SimilarityRetriever($embedder, $vectorStore);
```
