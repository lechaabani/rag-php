<?php

declare(strict_types=1);

namespace RagPhp\Core\Event;

use RagPhp\Core\ValueObject\Document;

final class DocumentsRetrievedEvent
{
    /**
     * @param list<Document> $documents
     */
    public function __construct(
        private readonly string $query,
        private array $documents,
    ) {
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return list<Document>
     */
    public function getDocuments(): array
    {
        return $this->documents;
    }

    /**
     * @param list<Document> $documents
     */
    public function setDocuments(array $documents): void
    {
        $this->documents = $documents;
    }
}
