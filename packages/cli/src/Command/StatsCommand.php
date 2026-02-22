<?php

declare(strict_types=1);

namespace RagPhp\Cli\Command;

use RagPhp\Core\Contract\EmbedderInterface;
use RagPhp\Core\Contract\LlmInterface;
use RagPhp\Core\Contract\VectorStoreInterface;
use RagPhp\Core\Store\InMemoryVectorStore;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'rag:stats', description: 'Display RAG pipeline statistics')]
final class StatsCommand extends Command
{
    public function __construct(
        private readonly EmbedderInterface $embedder,
        private readonly VectorStoreInterface $vectorStore,
        private readonly LlmInterface $llm,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('RAG-PHP Pipeline Statistics');

        $rows = [
            ['Embedder', $this->embedder::class],
            ['Embedding Dimensions', (string) $this->embedder->getDimensions()],
            ['Vector Store', $this->vectorStore::class],
            ['LLM', $this->llm::class],
        ];

        if ($this->vectorStore instanceof InMemoryVectorStore) {
            $rows[] = ['Documents in Store', (string) $this->vectorStore->count()];
        }

        $io->table(['Component', 'Value'], $rows);

        return Command::SUCCESS;
    }
}
