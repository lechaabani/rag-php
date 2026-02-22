<?php

declare(strict_types=1);

namespace RagPhp\Cli\Command;

use RagPhp\Core\Pipeline\RagPipeline;
use RagPhp\Core\ValueObject\Document;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'rag:index', description: 'Index documents into the vector store')]
final class IndexCommand extends Command
{
    public function __construct(
        private readonly RagPipeline $pipeline,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('source', InputArgument::REQUIRED, 'Path to file or directory')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'File format filter', 'txt')
            ->addOption('recursive', 'r', InputOption::VALUE_NONE, 'Scan directory recursively');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $source = $input->getArgument('source');
        $format = $input->getOption('format');
        $recursive = $input->getOption('recursive');

        if (!file_exists($source)) {
            $io->error("Source not found: {$source}");

            return Command::FAILURE;
        }

        $documents = [];

        if (is_dir($source)) {
            $pattern = $recursive
                ? $source . '/**/*.' . $format
                : $source . '/*.' . $format;

            $files = glob($pattern, $recursive ? GLOB_BRACE : 0) ?: [];

            foreach ($files as $file) {
                if (!is_file($file)) {
                    continue;
                }

                $content = file_get_contents($file);

                if ($content !== false && $content !== '') {
                    $documents[] = new Document($content, metadata: [
                        'source' => $file,
                        'filename' => basename($file),
                    ]);
                }
            }
        } else {
            $content = file_get_contents($source);

            if ($content !== false) {
                $documents[] = new Document($content, metadata: [
                    'source' => $source,
                    'filename' => basename($source),
                ]);
            }
        }

        if ($documents === []) {
            $io->warning('No documents found to index.');

            return Command::SUCCESS;
        }

        $io->info(sprintf('Indexing %d document(s)...', count($documents)));
        $io->progressStart(count($documents));

        $this->pipeline->index($documents);

        $io->progressFinish();
        $io->success(sprintf('Successfully indexed %d document(s).', count($documents)));

        return Command::SUCCESS;
    }
}
