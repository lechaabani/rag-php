<?php

declare(strict_types=1);

namespace RagPhp\Cli\Command;

use RagPhp\Core\Contract\EmbedderInterface;
use RagPhp\Core\Contract\LlmInterface;
use RagPhp\Core\Contract\VectorStoreInterface;
use RagPhp\Core\ValueObject\Document;
use RagPhp\Core\ValueObject\Response;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'rag:test-connection', description: 'Verify connection to all RAG services')]
final class TestConnectionCommand extends Command
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
        $io->title('Testing RAG-PHP Connections');
        $allOk = true;

        // Test Embedder
        $io->write('  Embedder ... ');
        try {
            $vector = $this->embedder->embed('test connection');
            $io->writeln(sprintf('<info>OK</info> (%d dimensions)', count($vector)));
        } catch (\Throwable $e) {
            $io->writeln(sprintf('<error>FAIL</error> %s', $e->getMessage()));
            $allOk = false;
        }

        // Test Vector Store
        $io->write('  Vector Store ... ');
        try {
            $id = $this->vectorStore->store(
                new Document('__rag_test__', '__test__'),
                $this->embedder->embed('test'),
            );
            $this->vectorStore->delete($id);
            $io->writeln('<info>OK</info>');
        } catch (\Throwable $e) {
            $io->writeln(sprintf('<error>FAIL</error> %s', $e->getMessage()));
            $allOk = false;
        }

        // Test LLM
        $io->write('  LLM ... ');
        try {
            $response = $this->llm->complete('Reply with exactly: OK', []);
            $io->writeln(sprintf('<info>OK</info> (%d tokens)', $response->getTokensUsed()));
        } catch (\Throwable $e) {
            $io->writeln(sprintf('<error>FAIL</error> %s', $e->getMessage()));
            $allOk = false;
        }

        $io->newLine();

        if ($allOk) {
            $io->success('All services are connected and working.');
        } else {
            $io->error('Some services failed. Check your configuration.');
        }

        return $allOk ? Command::SUCCESS : Command::FAILURE;
    }
}
