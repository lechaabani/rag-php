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
use RagPhp\Core\Pipeline\RagPipeline;
use RagPhp\Core\ValueObject\Document;
use RagPhp\Core\ValueObject\Response;

#[CoversClass(RagPipeline::class)]
final class RagPipelineTest extends TestCase
{
    #[Test]
    public function it_indexes_documents(): void
    {
        $embedder = $this->createMock(EmbedderInterface::class);
        $vectorStore = $this->createMock(VectorStoreInterface::class);
        $retriever = $this->createMock(RetrieverInterface::class);
        $llm = $this->createMock(LlmInterface::class);

        $docs = [
            new Document('Document 1'),
            new Document('Document 2'),
        ];

        $embedder->expects(self::once())
            ->method('embedBatch')
            ->with(['Document 1', 'Document 2'])
            ->willReturn([[1.0, 0.0], [0.0, 1.0]]);

        $vectorStore->expects(self::exactly(2))
            ->method('store');

        $pipeline = RagPipeline::create()
            ->withEmbedder($embedder)
            ->withVectorStore($vectorStore)
            ->withRetriever($retriever)
            ->withLlm($llm)
            ->build();

        $pipeline->index($docs);
    }

    #[Test]
    public function it_queries_and_returns_response(): void
    {
        $embedder = $this->createMock(EmbedderInterface::class);
        $vectorStore = $this->createMock(VectorStoreInterface::class);
        $retriever = $this->createMock(RetrieverInterface::class);
        $llm = $this->createMock(LlmInterface::class);

        $contextDocs = [new Document('Returns are accepted within 30 days.')];

        $retriever->expects(self::once())
            ->method('retrieve')
            ->with('How to return?', 5)
            ->willReturn($contextDocs);

        $expectedResponse = new Response('You can return within 30 days.', $contextDocs, 100);

        $llm->expects(self::once())
            ->method('complete')
            ->willReturn($expectedResponse);

        $pipeline = RagPipeline::create()
            ->withEmbedder($embedder)
            ->withVectorStore($vectorStore)
            ->withRetriever($retriever)
            ->withLlm($llm)
            ->build();

        $response = $pipeline->query('How to return?');

        self::assertSame('You can return within 30 days.', $response->getAnswer());
        self::assertSame(100, $response->getTokensUsed());
        self::assertCount(1, $response->getSources());
    }
}
