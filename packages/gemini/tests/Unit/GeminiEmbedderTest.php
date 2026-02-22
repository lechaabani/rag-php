<?php

declare(strict_types=1);

namespace RagPhp\Gemini\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Core\Exception\EmbeddingException;
use RagPhp\Gemini\GeminiConfiguration;
use RagPhp\Gemini\GeminiEmbedder;

#[CoversClass(GeminiEmbedder::class)]
final class GeminiEmbedderTest extends TestCase
{
    #[Test]
    public function it_returns_correct_dimensions(): void
    {
        $embedder = new GeminiEmbedder('fake-key', 'text-embedding-004');
        self::assertSame(768, $embedder->getDimensions());

        $embedder = new GeminiEmbedder('fake-key', 'embedding-001');
        self::assertSame(768, $embedder->getDimensions());
    }

    #[Test]
    public function it_throws_for_unknown_model(): void
    {
        $embedder = new GeminiEmbedder('fake-key', 'unknown-model');

        $this->expectException(EmbeddingException::class);
        $embedder->getDimensions();
    }

    #[Test]
    public function it_accepts_configuration_object(): void
    {
        $config = new GeminiConfiguration('fake-key');
        $embedder = new GeminiEmbedder($config);

        self::assertSame(768, $embedder->getDimensions());
    }
}
