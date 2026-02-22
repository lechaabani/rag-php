<?php

declare(strict_types=1);

namespace RagPhp\Laravel;

use Illuminate\Support\ServiceProvider;
use RagPhp\Anthropic\AnthropicLlm;
use RagPhp\Core\Contract\EmbedderInterface;
use RagPhp\Core\Contract\LlmInterface;
use RagPhp\Core\Contract\RetrieverInterface;
use RagPhp\Core\Contract\VectorStoreInterface;
use RagPhp\Core\Event\EventDispatcher;
use RagPhp\Core\Pipeline\PromptTemplate;
use RagPhp\Core\Pipeline\RagPipeline;
use RagPhp\Core\Retriever\SimilarityRetriever;
use RagPhp\Core\Store\InMemoryVectorStore;
use RagPhp\Gemini\GeminiEmbedder;
use RagPhp\Gemini\GeminiLlm;
use RagPhp\Laravel\Console\RagIndexCommand;
use RagPhp\Laravel\Console\RagQueryCommand;
use RagPhp\Laravel\Console\RagStatsCommand;
use RagPhp\Mistral\MistralEmbedder;
use RagPhp\Mistral\MistralLlm;
use RagPhp\Ollama\OllamaEmbedder;
use RagPhp\Ollama\OllamaLlm;
use RagPhp\OpenAI\OpenAIEmbedder;
use RagPhp\OpenAI\OpenAILlm;
use RagPhp\PgVector\PgVectorStore;

final class RagPhpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/rag.php', 'rag');

        $this->registerEmbedder();
        $this->registerVectorStore();
        $this->registerRetriever();
        $this->registerLlm();
        $this->registerPipeline();
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/rag.php' => config_path('rag.php'),
            ], 'rag-config');

            $this->commands([
                RagIndexCommand::class,
                RagQueryCommand::class,
                RagStatsCommand::class,
            ]);
        }
    }

    private function registerEmbedder(): void
    {
        $this->app->singleton(EmbedderInterface::class, function ($app) {
            $config = $app['config']['rag.embedder'];

            return match ($config['provider']) {
                'openai' => new OpenAIEmbedder($config['api_key'], $config['model']),
                'gemini' => new GeminiEmbedder($config['api_key'], $config['model']),
                'ollama' => new OllamaEmbedder($config['base_uri'] ?? null, $config['model']),
                'mistral' => new MistralEmbedder($config['api_key'], $config['model']),
                default => throw new \InvalidArgumentException("Unknown embedder provider: {$config['provider']}"),
            };
        });
    }

    private function registerVectorStore(): void
    {
        $this->app->singleton(VectorStoreInterface::class, function ($app) {
            $config = $app['config']['rag.vector_store'];

            return match ($config['driver']) {
                'pgvector' => new PgVectorStore(
                    $app['db']->connection($config['connection'] ?? null)->getDoctrineConnection(),
                    $config['table'] ?? 'rag_documents',
                    $config['dimensions'] ?? 1536,
                ),
                'in_memory' => new InMemoryVectorStore(),
                default => throw new \InvalidArgumentException("Unknown vector store driver: {$config['driver']}"),
            };
        });
    }

    private function registerRetriever(): void
    {
        $this->app->singleton(RetrieverInterface::class, function ($app) {
            return new SimilarityRetriever(
                $app->make(EmbedderInterface::class),
                $app->make(VectorStoreInterface::class),
            );
        });
    }

    private function registerLlm(): void
    {
        $this->app->singleton(LlmInterface::class, function ($app) {
            $config = $app['config']['rag.llm'];

            return match ($config['provider']) {
                'openai' => new OpenAILlm($config['api_key'], $config['model'], $config['temperature'], $config['max_tokens']),
                'gemini' => new GeminiLlm($config['api_key'], $config['model'], $config['temperature'], $config['max_tokens']),
                'ollama' => new OllamaLlm($config['base_uri'] ?? null, $config['model'], $config['temperature']),
                'anthropic' => new AnthropicLlm($config['api_key'], $config['model'], $config['temperature'], $config['max_tokens']),
                'mistral' => new MistralLlm($config['api_key'], $config['model'], $config['temperature'], $config['max_tokens']),
                default => throw new \InvalidArgumentException("Unknown LLM provider: {$config['provider']}"),
            };
        });
    }

    private function registerPipeline(): void
    {
        $this->app->singleton(RagPipeline::class, function ($app) {
            $template = $app['config']['rag.prompt.template'] ?? null;

            return new RagPipeline(
                embedder: $app->make(EmbedderInterface::class),
                vectorStore: $app->make(VectorStoreInterface::class),
                retriever: $app->make(RetrieverInterface::class),
                llm: $app->make(LlmInterface::class),
                promptTemplate: new PromptTemplate($template),
                eventDispatcher: new EventDispatcher(),
            );
        });
    }
}
