<?php

declare(strict_types=1);

namespace RagPhp\Gemini\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Gemini\GeminiConfiguration;
use RagPhp\Gemini\GeminiLlm;

#[CoversClass(GeminiLlm::class)]
final class GeminiLlmTest extends TestCase
{
    #[Test]
    public function it_accepts_string_api_key(): void
    {
        $llm = new GeminiLlm('fake-key');
        self::assertInstanceOf(GeminiLlm::class, $llm);
    }

    #[Test]
    public function it_accepts_configuration_object(): void
    {
        $config = new GeminiConfiguration('fake-key');
        $llm = new GeminiLlm($config, model: 'gemini-2.5-pro');
        self::assertInstanceOf(GeminiLlm::class, $llm);
    }
}
