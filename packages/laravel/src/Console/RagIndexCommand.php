<?php

declare(strict_types=1);

namespace RagPhp\Laravel\Console;

use Illuminate\Console\Command;
use RagPhp\Core\Pipeline\RagPipeline;
use RagPhp\Core\ValueObject\Document;

final class RagIndexCommand extends Command
{
    protected $signature = 'rag:index {source : Path to file or directory to index}
                            {--format=txt : File format (txt, md, csv, json)}';

    protected $description = 'Index documents into the vector store';

    public function handle(RagPipeline $pipeline): int
    {
        $source = $this->argument('source');

        if (!file_exists($source)) {
            $this->error("Source not found: {$source}");

            return self::FAILURE;
        }

        $documents = [];

        if (is_dir($source)) {
            $files = glob($source . '/*.' . $this->option('format')) ?: [];
            foreach ($files as $file) {
                $content = file_get_contents($file);
                if ($content !== false) {
                    $documents[] = new Document($content, metadata: ['source' => basename($file)]);
                }
            }
        } else {
            $content = file_get_contents($source);
            if ($content !== false) {
                $documents[] = new Document($content, metadata: ['source' => basename($source)]);
            }
        }

        if ($documents === []) {
            $this->warn('No documents found to index.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Indexing %d document(s)...', count($documents)));

        $pipeline->index($documents);

        $this->components->info(sprintf('Successfully indexed %d document(s).', count($documents)));

        return self::SUCCESS;
    }
}
