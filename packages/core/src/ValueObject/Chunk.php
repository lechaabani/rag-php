<?php

declare(strict_types=1);

namespace RagPhp\Core\ValueObject;

final readonly class Chunk
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        public string $content,
        public int $index,
        public array $metadata = [],
    ) {
    }

    public function toDocument(): Document
    {
        return new Document(
            content: $this->content,
            metadata: array_merge($this->metadata, ['chunk_index' => $this->index]),
        );
    }
}
