<?php

declare(strict_types=1);

namespace RagPhp\Ollama\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Core\Exception\EmbeddingException;
use RagPhp\Ollama\OllamaConfiguration;
use RagPhp\Ollama\OllamaEmbedder;

#[CoversClass(OllamaEmbedder::class)]
final class OllamaEmbedderTest extends TestCase
{
    #[Test]
    public function it_returns_correct_dimensions(): void
    {
        $embedder = new OllamaEmbedder(model: 'nomic-embed-text');
        self::assertSame(768, $embedder->getDimensions());

        $embedder = new OllamaEmbedder(model: 'mxbai-embed-large');
        self::assertSame(1024, $embedder->getDimensions());

        $embedder = new OllamaEmbedder(model: 'all-minilm');
        self::assertSame(384, $embedder->getDimensions());
    }

    #[Test]
    public function it_throws_for_unknown_model(): void
    {
        $embedder = new OllamaEmbedder(model: 'unknown');
        $this->expectException(EmbeddingException::class);
        $embedder->getDimensions();
    }

    #[Test]
    public function it_accepts_configuration_object(): void
    {
        $config = new OllamaConfiguration('http://custom:11434');
        $embedder = new OllamaEmbedder($config);
        self::assertSame(768, $embedder->getDimensions());
    }

    #[Test]
    public function it_defaults_to_localhost(): void
    {
        $embedder = new OllamaEmbedder();
        self::assertInstanceOf(OllamaEmbedder::class, $embedder);
    }
}
