---
sidebar_position: 2
---

# Recipe: Semantic Search Engine

Build a search engine that understands meaning, not just keywords.

## Why Semantic Search?

Traditional search matches exact keywords. Semantic search understands that "how to send back an item" means the same as "return policy".

## Implementation

```php
use RagPhp\Core\Retriever\SimilarityRetriever;
use RagPhp\Core\ValueObject\Document;
use RagPhp\OpenAI\OpenAIEmbedder;
use RagPhp\PgVector\PgVectorStore;

$embedder = new OpenAIEmbedder($apiKey);
$store = new PgVectorStore($connection, dimensions: 1536);
$retriever = new SimilarityRetriever($embedder, $store);

// Index your content
$documents = [];
foreach ($articles as $article) {
    $documents[] = new Document(
        content: $article['body'],
        metadata: [
            'title' => $article['title'],
            'url' => $article['url'],
            'category' => $article['category'],
        ],
    );
}

// Batch index
$vectors = $embedder->embedBatch(
    array_map(fn ($d) => $d->content, $documents)
);

foreach ($documents as $i => $doc) {
    $store->store($doc, $vectors[$i], $doc->metadata);
}

// Search with filters
$results = $retriever->retrieve('deployment best practices', topK: 10);

foreach ($results as $doc) {
    echo sprintf(
        "[%.2f] %s - %s\n",
        $doc->score,
        $doc->metadata['title'],
        $doc->metadata['url'],
    );
}
```

## With Category Filtering

```php
$results = $store->search(
    queryVector: $embedder->embed('deployment'),
    topK: 10,
    filters: ['category' => 'devops'],
);
```
