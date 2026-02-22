<?php

declare(strict_types=1);

namespace RagPhp\Core\ValueObject;

final readonly class Response
{
    /**
     * @param list<Document> $sources
     */
    public function __construct(
        private string $answer,
        private array $sources = [],
        private int $tokensUsed = 0,
    ) {
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    /**
     * @return list<Document>
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    public function getTokensUsed(): int
    {
        return $this->tokensUsed;
    }
}
