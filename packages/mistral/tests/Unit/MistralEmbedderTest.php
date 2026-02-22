<?php

declare(strict_types=1);

namespace RagPhp\Mistral\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Core\Exception\EmbeddingException;
use RagPhp\Mistral\MistralConfiguration;
use RagPhp\Mistral\MistralEmbedder;

#[CoversClass(MistralEmbedder::class)]
final class MistralEmbedderTest extends TestCase
{
    #[Test]
    public function it_returns_correct_dimensions(): void
    {
        $embedder = new MistralEmbedder('fake-key', 'mistral-embed');
        self::assertSame(1024, $embedder->getDimensions());
    }

    #[Test]
    public function it_throws_for_unknown_model(): void
    {
        $embedder = new MistralEmbedder('fake-key', 'unknown');
        $this->expectException(EmbeddingException::class);
        $embedder->getDimensions();
    }

    #[Test]
    public function it_accepts_configuration_object(): void
    {
        $config = new MistralConfiguration('fake-key');
        $embedder = new MistralEmbedder($config);
        self::assertSame(1024, $embedder->getDimensions());
    }
}
