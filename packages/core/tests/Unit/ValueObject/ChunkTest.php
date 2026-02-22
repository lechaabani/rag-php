<?php

declare(strict_types=1);

namespace RagPhp\Core\Tests\Unit\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Core\ValueObject\Chunk;

#[CoversClass(Chunk::class)]
final class ChunkTest extends TestCase
{
    #[Test]
    public function it_creates_a_chunk(): void
    {
        $chunk = new Chunk('chunk content', 0, ['page' => 1]);

        self::assertSame('chunk content', $chunk->content);
        self::assertSame(0, $chunk->index);
        self::assertSame(['page' => 1], $chunk->metadata);
    }

    #[Test]
    public function it_converts_to_document(): void
    {
        $chunk = new Chunk('text', 3, ['source' => 'file.pdf']);
        $doc = $chunk->toDocument();

        self::assertSame('text', $doc->content);
        self::assertSame('file.pdf', $doc->metadata['source']);
        self::assertSame(3, $doc->metadata['chunk_index']);
    }
}
