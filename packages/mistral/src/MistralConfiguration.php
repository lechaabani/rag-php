<?php

declare(strict_types=1);

namespace RagPhp\Mistral;

final readonly class MistralConfiguration
{
    public function __construct(
        public string $apiKey,
        public string $baseUri = 'https://api.mistral.ai/v1',
    ) {
    }
}
