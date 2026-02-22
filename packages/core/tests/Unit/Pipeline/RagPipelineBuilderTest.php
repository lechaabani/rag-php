<?php

declare(strict_types=1);

namespace RagPhp\Core\Tests\Unit\Pipeline;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Core\Contract\EmbedderInterface;
use RagPhp\Core\Contract\LlmInterface;
use RagPhp\Core\Contract\RetrieverInterface;
use RagPhp\Core\Contract\VectorStoreInterface;
use RagPhp\Core\Exception\RagException;
use RagPhp\Core\Pipeline\RagPipeline;
use RagPhp\Core\Pipeline\RagPipelineBuilder;

#[CoversClass(RagPipelineBuilder::class)]
final class RagPipelineBuilderTest extends TestCase
{
    #[Test]
    public function it_builds_a_pipeline_with_all_components(): void
    {
        $pipeline = (new RagPipelineBuilder())
            ->withEmbedder($this->createMock(EmbedderInterface::class))
            ->withVectorStore($this->createMock(VectorStoreInterface::class))
            ->withRetriever($this->createMock(RetrieverInterface::class))
            ->withLlm($this->createMock(LlmInterface::class))
            ->build();

        self::assertInstanceOf(RagPipeline::class, $pipeline);
    }

    #[Test]
    public function it_throws_without_embedder(): void
    {
        $this->expectException(RagException::class);
        $this->expectExceptionMessage('EmbedderInterface');

        (new RagPipelineBuilder())
            ->withVectorStore($this->createMock(VectorStoreInterface::class))
            ->withRetriever($this->createMock(RetrieverInterface::class))
            ->withLlm($this->createMock(LlmInterface::class))
            ->build();
    }

    #[Test]
    public function it_throws_without_vector_store(): void
    {
        $this->expectException(RagException::class);
        $this->expectExceptionMessage('VectorStoreInterface');

        (new RagPipelineBuilder())
            ->withEmbedder($this->createMock(EmbedderInterface::class))
            ->withRetriever($this->createMock(RetrieverInterface::class))
            ->withLlm($this->createMock(LlmInterface::class))
            ->build();
    }

    #[Test]
    public function it_throws_without_retriever(): void
    {
        $this->expectException(RagException::class);
        $this->expectExceptionMessage('RetrieverInterface');

        (new RagPipelineBuilder())
            ->withEmbedder($this->createMock(EmbedderInterface::class))
            ->withVectorStore($this->createMock(VectorStoreInterface::class))
            ->withLlm($this->createMock(LlmInterface::class))
            ->build();
    }

    #[Test]
    public function it_throws_without_llm(): void
    {
        $this->expectException(RagException::class);
        $this->expectExceptionMessage('LlmInterface');

        (new RagPipelineBuilder())
            ->withEmbedder($this->createMock(EmbedderInterface::class))
            ->withVectorStore($this->createMock(VectorStoreInterface::class))
            ->withRetriever($this->createMock(RetrieverInterface::class))
            ->build();
    }

    #[Test]
    public function create_static_method_returns_builder(): void
    {
        $builder = RagPipeline::create();

        self::assertInstanceOf(RagPipelineBuilder::class, $builder);
    }
}
