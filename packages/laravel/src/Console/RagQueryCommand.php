<?php

declare(strict_types=1);

namespace RagPhp\Laravel\Console;

use Illuminate\Console\Command;
use RagPhp\Core\Pipeline\RagPipeline;

final class RagQueryCommand extends Command
{
    protected $signature = 'rag:query {question : The question to ask}
                            {--top-k=5 : Number of documents to retrieve}';

    protected $description = 'Execute a RAG query interactively';

    public function handle(RagPipeline $pipeline): int
    {
        $question = $this->argument('question');
        $topK = (int) $this->option('top-k');

        $this->info("Querying: {$question}");
        $this->newLine();

        $response = $pipeline->query($question, $topK);

        $this->components->twoColumnDetail('Answer', '');
        $this->line($response->getAnswer());
        $this->newLine();

        $this->components->twoColumnDetail('Tokens used', (string) $response->getTokensUsed());
        $this->components->twoColumnDetail('Sources', (string) count($response->getSources()));

        return self::SUCCESS;
    }
}
