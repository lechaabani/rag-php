<?php

declare(strict_types=1);

namespace RagPhp\Core\Contract;

use RagPhp\Core\ValueObject\Document;

interface RetrieverInterface
{
    /**
     * Retrieve the most relevant documents for a given query.
     *
     * @return list<Document>
     */
    public function retrieve(string $query, int $topK = 5): array;
}
