<?php

declare(strict_types=1);

namespace RagPhp\Core\Contract;

use RagPhp\Core\ValueObject\Document;
use RagPhp\Core\ValueObject\Response;

interface LlmInterface
{
    /**
     * Generate a completion from a prompt with context documents.
     *
     * @param list<Document> $context
     */
    public function complete(string $prompt, array $context): Response;

    /**
     * Stream a completion, yielding text chunks as they arrive.
     *
     * @param list<Document> $context
     * @return \Generator<int, string, mixed, void>
     */
    public function stream(string $prompt, array $context): \Generator;
}
