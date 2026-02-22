<?php

declare(strict_types=1);

namespace RagPhp\Core\Event;

final class PromptBuiltEvent
{
    public function __construct(
        private string $prompt,
    ) {
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function setPrompt(string $prompt): void
    {
        $this->prompt = $prompt;
    }
}
