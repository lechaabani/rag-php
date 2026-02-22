<?php

declare(strict_types=1);

namespace RagPhp\Mistral\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Mistral\MistralConfiguration;
use RagPhp\Mistral\MistralLlm;

#[CoversClass(MistralLlm::class)]
final class MistralLlmTest extends TestCase
{
    #[Test]
    public function it_accepts_string_api_key(): void
    {
        $llm = new MistralLlm('fake-key');
        self::assertInstanceOf(MistralLlm::class, $llm);
    }

    #[Test]
    public function it_accepts_configuration_object(): void
    {
        $config = new MistralConfiguration('fake-key');
        $llm = new MistralLlm($config, model: 'mistral-small-latest');
        self::assertInstanceOf(MistralLlm::class, $llm);
    }
}
