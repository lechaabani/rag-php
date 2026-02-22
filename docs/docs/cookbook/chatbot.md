---
sidebar_position: 1
---

# Recipe: Customer Support Chatbot

Build a customer support chatbot that answers questions using your documentation.

## The Prompt

```php
use RagPhp\Core\Pipeline\PromptTemplate;

$template = new PromptTemplate(
    'You are a friendly customer support assistant. '
    . 'Answer using ONLY the context provided. '
    . 'If unsure, say "I will connect you with a human agent." '
    . "\n\nContext:\n{context}\n\nCustomer: {question}\n\nAssistant:"
);
```

## Full Pipeline

```php
use RagPhp\Core\Pipeline\RagPipeline;
use RagPhp\Core\Retriever\SimilarityRetriever;
use RagPhp\Core\ValueObject\Document;
use RagPhp\OpenAI\OpenAIEmbedder;
use RagPhp\OpenAI\OpenAILlm;
use RagPhp\PgVector\PgVectorStore;

$embedder = new OpenAIEmbedder($apiKey);
$store = new PgVectorStore($connection);
$retriever = new SimilarityRetriever($embedder, $store);
$llm = new OpenAILlm($apiKey, model: 'gpt-4o-mini', temperature: 0.1);

$chatbot = RagPipeline::create()
    ->withEmbedder($embedder)
    ->withVectorStore($store)
    ->withRetriever($retriever)
    ->withLlm($llm)
    ->withPrompt($template)
    ->build();

// Index your FAQ
$chatbot->index([
    new Document('Returns accepted within 30 days with original packaging.'),
    new Document('Free shipping on orders over $50.'),
    new Document('Contact us at support@store.com or 1-800-STORE.'),
]);

// Handle customer questions
$response = $chatbot->query('How do I return something?');
echo $response->getAnswer();
```

## Streaming Response (real-time)

```php
// For a chat UI, stream the response
foreach ($llm->stream($prompt, $context) as $chunk) {
    echo $chunk; // Send to frontend via SSE
    flush();
}
```

## Tips

- Use `temperature: 0.1` for support bots (more deterministic)
- Index your FAQ, policies, and product docs
- Use metadata filters to scope answers to relevant categories
- Monitor `$response->getTokensUsed()` for cost control
