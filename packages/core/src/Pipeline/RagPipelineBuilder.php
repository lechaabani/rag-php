<?php

declare(strict_types=1);

namespace RagPhp\Core\Pipeline;

use RagPhp\Core\Contract\EmbedderInterface;
use RagPhp\Core\Contract\LlmInterface;
use RagPhp\Core\Contract\RetrieverInterface;
use RagPhp\Core\Contract\VectorStoreInterface;
use RagPhp\Core\Event\EventDispatcher;
use RagPhp\Core\Exception\RagException;

final class RagPipelineBuilder
{
    private ?EmbedderInterface $embedder = null;
    private ?VectorStoreInterface $vectorStore = null;
    private ?RetrieverInterface $retriever = null;
    private ?LlmInterface $llm = null;
    private ?PromptTemplate $promptTemplate = null;
    private ?EventDispatcher $eventDispatcher = null;

    public function withEmbedder(EmbedderInterface $embedder): self
    {
        $this->embedder = $embedder;

        return $this;
    }

    public function withVectorStore(VectorStoreInterface $vectorStore): self
    {
        $this->vectorStore = $vectorStore;

        return $this;
    }

    public function withRetriever(RetrieverInterface $retriever): self
    {
        $this->retriever = $retriever;

        return $this;
    }

    public function withLlm(LlmInterface $llm): self
    {
        $this->llm = $llm;

        return $this;
    }

    public function withPrompt(PromptTemplate $promptTemplate): self
    {
        $this->promptTemplate = $promptTemplate;

        return $this;
    }

    public function withEventDispatcher(EventDispatcher $eventDispatcher): self
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    public function build(): RagPipeline
    {
        if ($this->embedder === null) {
            throw new RagException('An EmbedderInterface implementation is required.');
        }

        if ($this->vectorStore === null) {
            throw new RagException('A VectorStoreInterface implementation is required.');
        }

        if ($this->retriever === null) {
            throw new RagException('A RetrieverInterface implementation is required.');
        }

        if ($this->llm === null) {
            throw new RagException('A LlmInterface implementation is required.');
        }

        return new RagPipeline(
            embedder: $this->embedder,
            vectorStore: $this->vectorStore,
            retriever: $this->retriever,
            llm: $this->llm,
            promptTemplate: $this->promptTemplate ?? new PromptTemplate(),
            eventDispatcher: $this->eventDispatcher ?? new EventDispatcher(),
        );
    }
}
