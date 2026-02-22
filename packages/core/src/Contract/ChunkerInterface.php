<?php

declare(strict_types=1);

namespace RagPhp\Core\Contract;

use RagPhp\Core\ValueObject\Chunk;
use RagPhp\Core\ValueObject\Document;

interface ChunkerInterface
{
    /**
     * Split a document into smaller chunks.
     *
     * @return list<Chunk>
     */
    public function chunk(Document $document): array;
}
