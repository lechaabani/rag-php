---
sidebar_position: 8
---

# Events

RAG-PHP includes an event system that lets you hook into every stage of the pipeline without modifying the core.

## Available Events

| Event | When | Mutable |
|-------|------|---------|
| `QueryReceivedEvent` | Before embedding the question | Yes (query) |
| `DocumentsRetrievedEvent` | After retrieval, before generation | Yes (documents) |
| `PromptBuiltEvent` | After prompt construction | Yes (prompt) |
| `ResponseGeneratedEvent` | After LLM response | No (read-only) |

## Registering Listeners

```php
use RagPhp\Core\Event\EventDispatcher;
use RagPhp\Core\Event\QueryReceivedEvent;
use RagPhp\Core\Event\DocumentsRetrievedEvent;

$dispatcher = new EventDispatcher();

// Modify the query before embedding
$dispatcher->listen(QueryReceivedEvent::class, function (QueryReceivedEvent $event) {
    $query = $event->getQuery();
    $event->setQuery("In the context of our product: $query");
});

// Log retrieved documents
$dispatcher->listen(DocumentsRetrievedEvent::class, function (DocumentsRetrievedEvent $event) {
    foreach ($event->getDocuments() as $doc) {
        logger()->info('Retrieved: ' . substr($doc->content, 0, 100));
    }
});

// Use in pipeline
$rag = RagPipeline::create()
    ->withEventDispatcher($dispatcher)
    // ... other components
    ->build();
```

## Use Cases

- **Query rewriting** — Enrich or modify queries before embedding
- **Document filtering** — Remove or rerank documents after retrieval
- **Prompt injection** — Add system instructions to the prompt
- **Logging & monitoring** — Track pipeline performance
- **Caching** — Cache responses for repeated queries
