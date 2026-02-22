<?php

declare(strict_types=1);

namespace RagPhp\SymfonyBundle\DependencyInjection;

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
use RagPhp\Mistral\MistralEmbedder;
use RagPhp\Mistral\MistralLlm;
use RagPhp\Ollama\OllamaEmbedder;
use RagPhp\Ollama\OllamaLlm;
use RagPhp\OpenAI\OpenAIEmbedder;
use RagPhp\OpenAI\OpenAILlm;
use RagPhp\PgVector\PgVectorStore;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

final class RagPhpExtension extends Extension
{
    /**
     * @param array<string, mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerEmbedder($container, $config['embedder']);
        $this->registerVectorStore($container, $config['vector_store']);
        $this->registerRetriever($container, $config['retriever']);
        $this->registerLlm($container, $config['llm']);
        $this->registerPipeline($container, $config);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function registerEmbedder(ContainerBuilder $container, array $config): void
    {
        $definition = match ($config['provider']) {
            'openai' => new Definition(OpenAIEmbedder::class, [
                $config['api_key'],
                $config['model'],
            ]),
            'gemini' => new Definition(GeminiEmbedder::class, [
                $config['api_key'],
                $config['model'],
            ]),
            'ollama' => new Definition(OllamaEmbedder::class, [
                $config['base_uri'] ?? null,
                $config['model'],
            ]),
            'mistral' => new Definition(MistralEmbedder::class, [
                $config['api_key'],
                $config['model'],
            ]),
            default => throw new \InvalidArgumentException(sprintf('Unknown embedder provider: %s', $config['provider'])),
        };

        $container->setDefinition(EmbedderInterface::class, $definition);
        $container->setAlias('rag.embedder', EmbedderInterface::class);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function registerVectorStore(ContainerBuilder $container, array $config): void
    {
        $definition = match ($config['driver']) {
            'pgvector' => (new Definition(PgVectorStore::class, [
                new Reference($config['connection'] ?? 'doctrine.dbal.default_connection'),
                $config['table'] ?? 'rag_documents',
                $config['dimensions'] ?? 1536,
            ])),
            'in_memory' => new Definition(InMemoryVectorStore::class),
            default => throw new \InvalidArgumentException(sprintf('Unknown vector store driver: %s', $config['driver'])),
        };

        $container->setDefinition(VectorStoreInterface::class, $definition);
        $container->setAlias('rag.vector_store', VectorStoreInterface::class);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function registerRetriever(ContainerBuilder $container, array $config): void
    {
        $definition = new Definition(SimilarityRetriever::class, [
            new Reference(EmbedderInterface::class),
            new Reference(VectorStoreInterface::class),
        ]);

        $container->setDefinition(RetrieverInterface::class, $definition);
        $container->setAlias('rag.retriever', RetrieverInterface::class);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function registerLlm(ContainerBuilder $container, array $config): void
    {
        $definition = match ($config['provider']) {
            'openai' => new Definition(OpenAILlm::class, [
                $config['api_key'],
                $config['model'],
                $config['temperature'] ?? 0.2,
                $config['max_tokens'] ?? 2048,
            ]),
            'gemini' => new Definition(GeminiLlm::class, [
                $config['api_key'],
                $config['model'],
                $config['temperature'] ?? 0.2,
                $config['max_tokens'] ?? 2048,
            ]),
            'ollama' => new Definition(OllamaLlm::class, [
                $config['base_uri'] ?? null,
                $config['model'],
                $config['temperature'] ?? 0.2,
            ]),
            'anthropic' => new Definition(AnthropicLlm::class, [
                $config['api_key'],
                $config['model'],
                $config['temperature'] ?? 0.2,
                $config['max_tokens'] ?? 2048,
            ]),
            'mistral' => new Definition(MistralLlm::class, [
                $config['api_key'],
                $config['model'],
                $config['temperature'] ?? 0.2,
                $config['max_tokens'] ?? 2048,
            ]),
            default => throw new \InvalidArgumentException(sprintf('Unknown LLM provider: %s', $config['provider'])),
        };

        $container->setDefinition(LlmInterface::class, $definition);
        $container->setAlias('rag.llm', LlmInterface::class);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function registerPipeline(ContainerBuilder $container, array $config): void
    {
        $container->setDefinition(EventDispatcher::class, new Definition(EventDispatcher::class));

        $promptDef = new Definition(PromptTemplate::class, [
            $config['prompt']['template'] ?? null,
        ]);
        $container->setDefinition(PromptTemplate::class, $promptDef);

        $pipelineDef = new Definition(RagPipeline::class, [
            new Reference(EmbedderInterface::class),
            new Reference(VectorStoreInterface::class),
            new Reference(RetrieverInterface::class),
            new Reference(LlmInterface::class),
            new Reference(PromptTemplate::class),
            new Reference(EventDispatcher::class),
        ]);
        $pipelineDef->setPublic(true);

        $container->setDefinition(RagPipeline::class, $pipelineDef);
        $container->setAlias('rag.pipeline', RagPipeline::class);
    }
}
