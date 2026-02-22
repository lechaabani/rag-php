<?php

declare(strict_types=1);

namespace RagPhp\Anthropic;

final readonly class AnthropicConfiguration
{
    public function __construct(
        public string $apiKey,
        public string $baseUri = 'https://api.anthropic.com/v1',
    ) {
    }
}
