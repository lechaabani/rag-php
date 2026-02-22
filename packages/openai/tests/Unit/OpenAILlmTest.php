<?php

declare(strict_types=1);

namespace RagPhp\OpenAI\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\OpenAI\OpenAIConfiguration;
use RagPhp\OpenAI\OpenAILlm;

#[CoversClass(OpenAILlm::class)]
#[CoversClass(OpenAIConfiguration::class)]
final class OpenAILlmTest extends TestCase
{
    #[Test]
    public function it_accepts_string_api_key(): void
    {
        $llm = new OpenAILlm('fake-key');

        self::assertInstanceOf(OpenAILlm::class, $llm);
    }

    #[Test]
    public function it_accepts_configuration_object(): void
    {
        $config = new OpenAIConfiguration('fake-key', 'org-123');
        $llm = new OpenAILlm($config);

        self::assertInstanceOf(OpenAILlm::class, $llm);
    }
}
