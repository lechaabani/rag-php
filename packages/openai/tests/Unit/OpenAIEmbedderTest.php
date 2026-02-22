<?php

declare(strict_types=1);

namespace RagPhp\OpenAI\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Core\Exception\EmbeddingException;
use RagPhp\OpenAI\OpenAIEmbedder;

#[CoversClass(OpenAIEmbedder::class)]
final class OpenAIEmbedderTest extends TestCase
{
    #[Test]
    public function it_returns_correct_dimensions_for_known_models(): void
    {
        $embedder = new OpenAIEmbedder('fake-key', 'text-embedding-3-small');
        self::assertSame(1536, $embedder->getDimensions());

        $embedder = new OpenAIEmbedder('fake-key', 'text-embedding-3-large');
        self::assertSame(3072, $embedder->getDimensions());

        $embedder = new OpenAIEmbedder('fake-key', 'text-embedding-ada-002');
        self::assertSame(1536, $embedder->getDimensions());
    }

    #[Test]
    public function it_throws_for_unknown_model_dimensions(): void
    {
        $embedder = new OpenAIEmbedder('fake-key', 'unknown-model');

        $this->expectException(EmbeddingException::class);
        $embedder->getDimensions();
    }
}
