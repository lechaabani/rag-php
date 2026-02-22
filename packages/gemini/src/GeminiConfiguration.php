<?php

declare(strict_types=1);

namespace RagPhp\Gemini;

final readonly class GeminiConfiguration
{
    public function __construct(
        public string $apiKey,
        public string $baseUri = 'https://generativelanguage.googleapis.com/v1beta',
    ) {
    }
}
