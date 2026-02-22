<?php

declare(strict_types=1);

namespace RagPhp\Core\Retriever;

use RagPhp\Core\Contract\EmbedderInterface;
use RagPhp\Core\Contract\RetrieverInterface;
use RagPhp\Core\Contract\VectorStoreInterface;
use RagPhp\Core\ValueObject\Document;

final readonly class SimilarityRetriever implements RetrieverInterface
{
    public function __construct(
        private EmbedderInterface $embedder,
        private VectorStoreInterface $vectorStore,
    ) {
    }

    /**
     * @return list<Document>
     */
    public function retrieve(string $query, int $topK = 5): array
    {
        $queryVector = $this->embedder->embed($query);

        return $this->vectorStore->search($queryVector, $topK);
    }
}
