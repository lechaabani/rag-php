<?php

declare(strict_types=1);

namespace RagPhp\Core\Tests\Unit\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Core\ValueObject\Document;

#[CoversClass(Document::class)]
final class DocumentTest extends TestCase
{
    #[Test]
    public function it_creates_a_document_with_content(): void
    {
        $doc = new Document('Hello world');

        self::assertSame('Hello world', $doc->content);
        self::assertSame('', $doc->id);
        self::assertSame([], $doc->metadata);
        self::assertSame(0.0, $doc->score);
    }

    #[Test]
    public function it_creates_a_document_with_all_properties(): void
    {
        $doc = new Document('content', 'doc-1', ['source' => 'test'], 0.95);

        self::assertSame('content', $doc->content);
        self::assertSame('doc-1', $doc->id);
        self::assertSame(['source' => 'test'], $doc->metadata);
        self::assertSame(0.95, $doc->score);
    }

    #[Test]
    public function with_id_returns_new_instance(): void
    {
        $doc = new Document('content');
        $withId = $doc->withId('new-id');

        self::assertNotSame($doc, $withId);
        self::assertSame('new-id', $withId->id);
        self::assertSame('content', $withId->content);
    }

    #[Test]
    public function with_score_returns_new_instance(): void
    {
        $doc = new Document('content');
        $withScore = $doc->withScore(0.85);

        self::assertNotSame($doc, $withScore);
        self::assertSame(0.85, $withScore->score);
    }

    #[Test]
    public function with_metadata_merges(): void
    {
        $doc = new Document('content', metadata: ['a' => 1]);
        $updated = $doc->withMetadata(['b' => 2]);

        self::assertSame(['a' => 1, 'b' => 2], $updated->metadata);
    }
}
