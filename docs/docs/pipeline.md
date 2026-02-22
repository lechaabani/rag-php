---
sidebar_position: 7
---

# Pipeline

The `RagPipeline` is the central orchestrator that connects all RAG components together.

## Building a Pipeline

Use the fluent builder API:

```php
use RagPhp\Core\Pipeline\RagPipeline;
use RagPhp\Core\Pipeline\PromptTemplate;

$rag = RagPipeline::create()
    ->withEmbedder($embedder)
    ->withVectorStore($vectorStore)
    ->withRetriever($retriever)
    ->withLlm($llm)
    ->withPrompt(new PromptTemplate($customTemplate))  // optional
    ->withEventDispatcher($eventDispatcher)             // optional
    ->build();
```

All four core components are required: embedder, vector store, retriever, and LLM. The prompt template and event dispatcher are optional (sensible defaults are provided).

## Indexing Documents

```php
use RagPhp\Core\ValueObject\Document;

$rag->index([
    new Document('Returns policy content...', metadata: ['source' => 'faq']),
    new Document('Shipping information...', metadata: ['source' => 'docs']),
]);
```

The pipeline automatically embeds each document and stores it in the vector store.

## Querying

```php
$response = $rag->query('How do I return a product?', topK: 5);

echo $response->getAnswer();      // Generated answer
echo $response->getTokensUsed();  // Token usage
$response->getSources();           // Retrieved source documents
```

## Custom Prompt Templates

```php
use RagPhp\Core\Pipeline\PromptTemplate;

$template = new PromptTemplate(<<<'PROMPT'
You are a helpful customer support assistant.
Use ONLY the following context to answer. If unsure, say "I don't know."

Context:
{context}

Customer question: {question}

Answer:
PROMPT);

$rag = RagPipeline::create()
    ->withPrompt($template)
    // ... other components
    ->build();
```

Placeholders `{question}` and `{context}` are replaced at runtime.
