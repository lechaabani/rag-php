<?php

declare(strict_types=1);

namespace RagPhp\Anthropic\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Anthropic\AnthropicConfiguration;
use RagPhp\Anthropic\AnthropicLlm;

#[CoversClass(AnthropicLlm::class)]
final class AnthropicLlmTest extends TestCase
{
    #[Test]
    public function it_accepts_string_api_key(): void
    {
        $llm = new AnthropicLlm('fake-key');
        self::assertInstanceOf(AnthropicLlm::class, $llm);
    }

    #[Test]
    public function it_accepts_configuration_object(): void
    {
        $config = new AnthropicConfiguration('fake-key');
        $llm = new AnthropicLlm($config, model: 'claude-sonnet-4-5-20250929');
        self::assertInstanceOf(AnthropicLlm::class, $llm);
    }

    #[Test]
    public function it_accepts_custom_model(): void
    {
        $llm = new AnthropicLlm('fake-key', model: 'claude-haiku-4-5-20251001');
        self::assertInstanceOf(AnthropicLlm::class, $llm);
    }
}
