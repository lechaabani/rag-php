<?php

declare(strict_types=1);

namespace RagPhp\Laravel\Console;

use Illuminate\Console\Command;
use RagPhp\Core\Contract\EmbedderInterface;
use RagPhp\Core\Contract\VectorStoreInterface;
use RagPhp\Core\Store\InMemoryVectorStore;

final class RagStatsCommand extends Command
{
    protected $signature = 'rag:stats';

    protected $description = 'Display RAG pipeline statistics';

    public function handle(EmbedderInterface $embedder, VectorStoreInterface $store): int
    {
        $this->components->twoColumnDetail('Embedder', $embedder::class);
        $this->components->twoColumnDetail('Dimensions', (string) $embedder->getDimensions());
        $this->components->twoColumnDetail('Vector Store', $store::class);

        if ($store instanceof InMemoryVectorStore) {
            $this->components->twoColumnDetail('Documents', (string) $store->count());
        }

        $this->components->twoColumnDetail('LLM Provider', config('rag.llm.provider', 'unknown'));
        $this->components->twoColumnDetail('LLM Model', config('rag.llm.model', 'unknown'));

        return self::SUCCESS;
    }
}
