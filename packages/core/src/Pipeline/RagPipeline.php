<?php

declare(strict_types=1);

namespace RagPhp\Core\Pipeline;

use RagPhp\Core\Contract\EmbedderInterface;
use RagPhp\Core\Contract\LlmInterface;
use RagPhp\Core\Contract\RetrieverInterface;
use RagPhp\Core\Contract\VectorStoreInterface;
use RagPhp\Core\Event\DocumentsRetrievedEvent;
use RagPhp\Core\Event\EventDispatcher;
use RagPhp\Core\Event\PromptBuiltEvent;
use RagPhp\Core\Event\QueryReceivedEvent;
use RagPhp\Core\Event\ResponseGeneratedEvent;
use RagPhp\Core\ValueObject\Document;
use RagPhp\Core\ValueObject\Response;

final class RagPipeline
{
    public function __construct(
        private readonly EmbedderInterface $embedder,
        private readonly VectorStoreInterface $vectorStore,
        private readonly RetrieverInterface $retriever,
        private readonly LlmInterface $llm,
        private readonly PromptTemplate $promptTemplate,
        private readonly EventDispatcher $eventDispatcher,
    ) {
    }

    public static function create(): RagPipelineBuilder
    {
        return new RagPipelineBuilder();
    }

    /**
     * Index documents into the vector store.
     *
     * @param list<Document> $documents
     */
    public function index(array $documents): void
    {
        $texts = array_map(static fn (Document $doc): string => $doc->content, $documents);
        $vectors = $this->embedder->embedBatch($texts);

        foreach ($documents as $i => $document) {
            $this->vectorStore->store($document, $vectors[$i], $document->metadata);
        }
    }

    /**
     * Execute a RAG query: retrieve context, build prompt, generate response.
     */
    public function query(string $query, int $topK = 5): Response
    {
        // 1. Dispatch query received event
        $queryEvent = $this->eventDispatcher->dispatch(new QueryReceivedEvent($query));
        $query = $queryEvent->getQuery();

        // 2. Retrieve relevant documents
        $documents = $this->retriever->retrieve($query, $topK);
        $docsEvent = $this->eventDispatcher->dispatch(new DocumentsRetrievedEvent($query, $documents));
        $documents = $docsEvent->getDocuments();

        // 3. Build prompt
        $prompt = $this->promptTemplate->render($query, $documents);
        $promptEvent = $this->eventDispatcher->dispatch(new PromptBuiltEvent($prompt));
        $prompt = $promptEvent->getPrompt();

        // 4. Generate response via LLM
        $response = $this->llm->complete($prompt, $documents);
        $this->eventDispatcher->dispatch(new ResponseGeneratedEvent($response));

        return $response;
    }
}
