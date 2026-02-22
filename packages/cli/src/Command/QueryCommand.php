<?php

declare(strict_types=1);

namespace RagPhp\Cli\Command;

use RagPhp\Core\Pipeline\RagPipeline;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'rag:query', description: 'Execute a RAG query')]
final class QueryCommand extends Command
{
    public function __construct(
        private readonly RagPipeline $pipeline,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('question', InputArgument::REQUIRED, 'The question to ask')
            ->addOption('top-k', 'k', InputOption::VALUE_REQUIRED, 'Number of documents to retrieve', '5')
            ->addOption('show-sources', 's', InputOption::VALUE_NONE, 'Show source documents');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $question = $input->getArgument('question');
        $topK = (int) $input->getOption('top-k');
        $showSources = $input->getOption('show-sources');

        $io->section('Query');
        $io->text($question);
        $io->newLine();

        $response = $this->pipeline->query($question, $topK);

        $io->section('Answer');
        $io->text($response->getAnswer());
        $io->newLine();

        $io->horizontalTable(
            ['Tokens Used', 'Sources Retrieved'],
            [[(string) $response->getTokensUsed(), (string) count($response->getSources())]],
        );

        if ($showSources) {
            $io->section('Source Documents');
            foreach ($response->getSources() as $i => $doc) {
                $io->text(sprintf(
                    '<info>[%d]</info> (score: %.4f) %s',
                    $i + 1,
                    $doc->score,
                    mb_substr($doc->content, 0, 200),
                ));
                $io->newLine();
            }
        }

        return Command::SUCCESS;
    }
}
