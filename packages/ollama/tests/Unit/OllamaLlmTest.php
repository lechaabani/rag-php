<?php

declare(strict_types=1);

namespace RagPhp\Ollama\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Ollama\OllamaConfiguration;
use RagPhp\Ollama\OllamaLlm;

#[CoversClass(OllamaLlm::class)]
final class OllamaLlmTest extends TestCase
{
    #[Test]
    public function it_defaults_to_localhost_and_llama3(): void
    {
        $llm = new OllamaLlm();
        self::assertInstanceOf(OllamaLlm::class, $llm);
    }

    #[Test]
    public function it_accepts_configuration_object(): void
    {
        $config = new OllamaConfiguration('http://gpu-server:11434');
        $llm = new OllamaLlm($config, model: 'mistral');
        self::assertInstanceOf(OllamaLlm::class, $llm);
    }

    #[Test]
    public function it_accepts_string_base_uri(): void
    {
        $llm = new OllamaLlm('http://remote:11434', model: 'gemma');
        self::assertInstanceOf(OllamaLlm::class, $llm);
    }
}
