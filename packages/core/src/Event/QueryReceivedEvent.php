<?php

declare(strict_types=1);

namespace RagPhp\Core\Event;

final class QueryReceivedEvent
{
    public function __construct(
        private string $query,
    ) {
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function setQuery(string $query): void
    {
        $this->query = $query;
    }
}
