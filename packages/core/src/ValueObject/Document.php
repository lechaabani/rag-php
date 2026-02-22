<?php

declare(strict_types=1);

namespace RagPhp\Core\ValueObject;

final readonly class Document
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        public string $content,
        public string $id = '',
        public array $metadata = [],
        public float $score = 0.0,
    ) {
    }

    public function withId(string $id): self
    {
        return new self($this->content, $id, $this->metadata, $this->score);
    }

    public function withScore(float $score): self
    {
        return new self($this->content, $this->id, $this->metadata, $score);
    }

    /**
     * @param array<string, mixed> $metadata
     */
    public function withMetadata(array $metadata): self
    {
        return new self($this->content, $this->id, array_merge($this->metadata, $metadata), $this->score);
    }
}
