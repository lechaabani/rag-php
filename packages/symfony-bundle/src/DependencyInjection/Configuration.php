<?php

declare(strict_types=1);

namespace RagPhp\SymfonyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('rag');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('embedder')
                    ->isRequired()
                    ->children()
                        ->enumNode('provider')
                            ->values(['openai', 'gemini', 'ollama', 'mistral'])
                            ->isRequired()
                        ->end()
                        ->scalarNode('model')->defaultValue('text-embedding-3-small')->end()
                        ->scalarNode('api_key')->defaultNull()->end()
                        ->scalarNode('base_uri')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('vector_store')
                    ->isRequired()
                    ->children()
                        ->enumNode('driver')
                            ->values(['pgvector', 'in_memory'])
                            ->isRequired()
                        ->end()
                        ->scalarNode('connection')->defaultValue('doctrine.dbal.default_connection')->end()
                        ->scalarNode('table')->defaultValue('rag_documents')->end()
                        ->integerNode('dimensions')->defaultValue(1536)->end()
                    ->end()
                ->end()
                ->arrayNode('retriever')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('strategy')
                            ->values(['similarity', 'mmr', 'hyde', 'bm25', 'hybrid'])
                            ->defaultValue('similarity')
                        ->end()
                        ->integerNode('top_k')->defaultValue(5)->end()
                    ->end()
                ->end()
                ->arrayNode('llm')
                    ->isRequired()
                    ->children()
                        ->enumNode('provider')
                            ->values(['openai', 'gemini', 'ollama', 'anthropic', 'mistral'])
                            ->isRequired()
                        ->end()
                        ->scalarNode('model')->defaultValue('gpt-4o')->end()
                        ->scalarNode('api_key')->defaultNull()->end()
                        ->scalarNode('base_uri')->defaultNull()->end()
                        ->floatNode('temperature')->defaultValue(0.2)->end()
                        ->integerNode('max_tokens')->defaultValue(2048)->end()
                    ->end()
                ->end()
                ->arrayNode('prompt')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('template')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
