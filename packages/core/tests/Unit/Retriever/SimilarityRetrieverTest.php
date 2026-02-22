<?php

declare(strict_types=1);

namespace RagPhp\Core\Tests\Unit\Retriever;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Core\Contract\EmbedderInterface;
use RagPhp\Core\Contract\VectorStoreInterface;
use RagPhp\Core\Retriever\SimilarityRetriever;
use RagPhp\Core\ValueObject\Document;

#[CoversClass(SimilarityRetriever::class)]
final class SimilarityRetrieverTest extends TestCase
{
    #[Test]
    public function it_embeds_query_and_searches_store(): void
    {
        $embedder = $this->createMock(EmbedderInterface::class);
        $store = $this->createMock(VectorStoreInterface::class);

        $queryVector = [1.0, 0.5, 0.0];
        $expectedDocs = [new Document('result doc', 'doc-1')];

        $embedder->expects(self::once())
            ->method('embed')
            ->with('search query')
            ->willReturn($queryVector);

        $store->expects(self::once())
            ->method('search')
            ->with($queryVector, 3)
            ->willReturn($expectedDocs);

        $retriever = new SimilarityRetriever($embedder, $store);
        $results = $retriever->retrieve('search query', 3);

        self::assertSame($expectedDocs, $results);
    }
}
