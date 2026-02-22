<?php

declare(strict_types=1);

/**
 * RAG-PHP Quick Start Example
 *
 * 1. Copy .env.example to .env and set your API key
 * 2. Run: docker compose -f docker/docker-compose.yml up -d
 * 3. Run: php src/example.php
 */

require __DIR__ . '/../vendor/autoload.php';

use RagPhp\Core\Pipeline\RagPipeline;
use RagPhp\Core\Retriever\SimilarityRetriever;
use RagPhp\Core\Store\InMemoryVectorStore;
use RagPhp\Core\ValueObject\Document;
use RagPhp\OpenAI\OpenAIEmbedder;
use RagPhp\OpenAI\OpenAILlm;

// Load .env
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines ?: [] as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        putenv(trim($line));
    }
}

$apiKey = getenv('OPENAI_API_KEY') ?: throw new RuntimeException(
    'Set OPENAI_API_KEY in .env file. Copy .env.example to .env to get started.',
);

// --- Configure components ---
$embedder = new OpenAIEmbedder($apiKey);
$store = new InMemoryVectorStore(); // Use PgVectorStore for production
$retriever = new SimilarityRetriever($embedder, $store);
$llm = new OpenAILlm($apiKey, model: 'gpt-4o-mini');

// --- Build pipeline ---
$rag = RagPipeline::create()
    ->withEmbedder($embedder)
    ->withVectorStore($store)
    ->withRetriever($retriever)
    ->withLlm($llm)
    ->build();

// --- Index some documents ---
echo "Indexing documents...\n";

$rag->index([
    new Document(
        'Returns are accepted within 30 days of purchase. Items must be in original packaging.',
        metadata: ['source' => 'returns-policy.md'],
    ),
    new Document(
        'Standard shipping takes 3-5 business days. Express shipping is 1-2 business days.',
        metadata: ['source' => 'shipping.md'],
    ),
    new Document(
        'Premium members get free expedited shipping and extended 60-day return windows.',
        metadata: ['source' => 'premium.md'],
    ),
    new Document(
        'Contact support at support@example.com or call 1-800-RAG-PHP for assistance.',
        metadata: ['source' => 'contact.md'],
    ),
]);

echo "Done! 4 documents indexed.\n\n";

// --- Query ---
$questions = [
    'How do I return a product?',
    'How long does shipping take?',
    'What are the benefits of premium membership?',
];

foreach ($questions as $question) {
    echo "Q: {$question}\n";
    $response = $rag->query($question);
    echo "A: {$response->getAnswer()}\n";
    echo "   (tokens: {$response->getTokensUsed()}, sources: " . count($response->getSources()) . ")\n\n";
}
