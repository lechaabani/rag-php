<?php

declare(strict_types=1);

namespace RagPhp\Ollama;

final readonly class OllamaConfiguration
{
    public function __construct(
        public string $baseUri = 'http://localhost:11434',
    ) {
    }
}
