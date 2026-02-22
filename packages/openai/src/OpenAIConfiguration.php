<?php

declare(strict_types=1);

namespace RagPhp\OpenAI;

final readonly class OpenAIConfiguration
{
    public function __construct(
        public string $apiKey,
        public string $organization = '',
        public string $baseUri = 'https://api.openai.com/v1',
    ) {
    }
}
